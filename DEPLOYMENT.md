# AWS Deployment Guide - Clinic Management System

## Overview
This guide helps you deploy the Clinic Management System to AWS using various options.

## Prerequisites
- AWS Account
- AWS CLI installed and configured
- Docker installed (for local testing)
- GitHub account for CI/CD integration

---

## Option 1: AWS ECS Fargate (Recommended - Serverless Containers)

### Benefits
- No server management
- Auto-scaling
- Pay-per-use pricing
- Easy updates

### Steps

#### 1. Create ECR Repository
```bash
aws ecr create-repository --repository-name clinic-management --region us-east-1
```

#### 2. Build and Push Docker Image
```bash
# Login to ECR
aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin YOUR_ACCOUNT_ID.dkr.ecr.us-east-1.amazonaws.com

# Build image
docker build -t clinic-management:latest -f docker/Dockerfile .

# Tag image
docker tag clinic-management:latest YOUR_ACCOUNT_ID.dkr.ecr.us-east-1.amazonaws.com/clinic-management:latest

# Push to ECR
docker push YOUR_ACCOUNT_ID.dkr.ecr.us-east-1.amazonaws.com/clinic-management:latest
```

#### 3. Set Up RDS Database (MySQL)
```bash
# Replace with your own values
aws rds create-db-instance \
  --db-instance-identifier clinic-management-db \
  --db-instance-class db.t3.micro \
  --engine mysql \
  --master-username admin \
  --master-user-password YOUR_PASSWORD \
  --allocated-storage 20 \
  --region us-east-1
```

#### 4. Create CloudWatch Log Group
```bash
aws logs create-log-group --log-group-name /ecs/clinic-management --region us-east-1
```

#### 5. Register ECS Task Definition
```bash
# Update aws/ecs-task-definition.json with your AWS Account ID and Region
aws ecs register-task-definition \
  --cli-input-json file://aws/ecs-task-definition.json \
  --region us-east-1
```

#### 6. Create ECS Cluster
```bash
aws ecs create-cluster --cluster-name clinic-management --region us-east-1
```

#### 7. Create ECS Service
```bash
aws ecs create-service \
  --cluster clinic-management \
  --service-name clinic-management-service \
  --task-definition clinic-management:1 \
  --desired-count 1 \
  --launch-type FARGATE \
  --network-configuration "awsvpcConfiguration={subnets=[subnet-xxx],securityGroups=[sg-xxx],assignPublicIp=ENABLED}" \
  --load-balancers targetGroupArn=arn:aws:elasticloadbalancing:...,containerName=clinic-management,containerPort=80 \
  --region us-east-1
```

---

## Option 2: AWS Elastic Beanstalk (Simplest)

### Steps

#### 1. Install EB CLI
```bash
pip install awsebcli
```

#### 2. Initialize Elastic Beanstalk
```bash
eb init -p docker clinic-management --region us-east-1
```

#### 3. Create Environment
```bash
eb create clinic-management-env
```

#### 4. Configure Environment Variables
```bash
# In .ebextensions/app.config
option_settings:
  aws:elasticbeanstalk:application:environment:
    APP_ENV: production
    DB_HOST: your-rds-endpoint.amazonaws.com
```

#### 5. Deploy
```bash
eb deploy
```

---

## Option 3: AWS EC2 (Manual Server)

### Steps

#### 1. Launch EC2 Instance
```bash
aws ec2 run-instances \
  --image-id ami-0c55b159cbfafe1f0 \
  --instance-type t2.micro \
  --key-name your-key-pair \
  --security-groups default \
  --region us-east-1
```

#### 2. SSH into Instance
```bash
ssh -i your-key.pem ec2-user@your-instance-ip
```

#### 3. Install Dependencies
```bash
sudo yum update -y
sudo yum install -y php php-mysql php-fpm nginx
sudo systemctl start php-fpm
sudo systemctl start nginx
```

#### 4. Clone Repository
```bash
cd /var/www/html
sudo git clone https://github.com/wamataifa/BONIPHACE-PHILBERT-MWAGALAZI-.git .
sudo chown -R nginx:nginx .
```

#### 5. Set Environment Variables
```bash
cp .env.example .env
# Edit .env with production values
```

#### 6. Database Migration
```bash
php -r "require 'cli.php'; migrate();"
# or import your SQL file
mysql -h your-rds-host -u admin -p clinic_management < clinic_management.sql
```

---

## Option 4: AWS Lightsail (Budget-Friendly)

### Steps

1. Go to [AWS Lightsail Console](https://lightsail.aws.amazon.com)
2. Click "Create Instance"
3. Choose "WordPress" or "LAMP Stack" blueprint
4. Select instance plan ($3.50-$10/month)
5. Create instance
6. SSH and follow EC2 steps above

---

## CI/CD Pipeline Setup (GitHub Actions)

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to AWS

on:
  push:
    branches: [main]

env:
  AWS_REGION: us-east-1
  ECR_REPOSITORY: clinic-management

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Configure AWS credentials
        uses: aws-actions/configure-aws-credentials@v1
        with:
          aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
          aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
          aws-region: ${{ env.AWS_REGION }}
      
      - name: Build and push Docker image
        run: |
          docker build -t clinic-management:latest -f docker/Dockerfile .
          docker tag clinic-management:latest ${{ secrets.AWS_ACCOUNT_ID }}.dkr.ecr.${{ env.AWS_REGION }}.amazonaws.com/${{ env.ECR_REPOSITORY }}:latest
          docker push ${{ secrets.AWS_ACCOUNT_ID }}.dkr.ecr.${{ env.AWS_REGION }}.amazonaws.com/${{ env.ECR_REPOSITORY }}:latest
```

---

## Database Migration (SQLite to MySQL)

Since your app uses SQLite, migrate to AWS RDS MySQL:

```bash
# Export SQLite schema
sqlite3 clinic_management.sqlite .schema > schema.sql

# Convert SQLite SQL to MySQL format
# (Review file and remove SQLite-specific syntax)

# Import to RDS
mysql -h your-rds-endpoint.amazonaws.com -u admin -p clinic_management < schema.sql
```

---

## Monitoring & Logging

### CloudWatch Logs
```bash
aws logs tail /ecs/clinic-management --follow
```

### Application Health
- ECS: AWS Console → ECS → Services → clinic-management-service
- Elastic Beanstalk: `eb health`
- EC2: SSH and check logs

---

## Cost Estimates (Monthly)

| Service | Cost |
|---------|------|
| ECS Fargate | ~$10-30 (0.256 CPU, 512 MB RAM) |
| RDS MySQL db.t3.micro | ~$10-15 |
| Load Balancer | ~$16 |
| NAT Gateway | ~$32 |
| **Total** | **~$70-90** |

---

## Security Checklist

- [ ] Enable HTTPS with ACM certificate
- [ ] Configure security groups (restrict SSH to your IP)
- [ ] Enable RDS encryption
- [ ] Store secrets in AWS Secrets Manager
- [ ] Enable CloudTrail logging
- [ ] Set up WAF rules
- [ ] Regular backups enabled
- [ ] Update .env variables before deployment

---

## Troubleshooting

### Connection refused on port 80
Check security group inbound rules allow port 80 and 443.

### Database connection failed
Verify RDS security group allows MySQL (3306) from ECS/EC2 security group.

### Permission denied on files
Ensure proper permissions: `chown -R www-data:www-data /var/www/html`

### High memory usage
Check PHP-FPM worker count and tune in nginx.conf

---

## Support
For issues or questions, create an issue on GitHub or consult AWS documentation.
