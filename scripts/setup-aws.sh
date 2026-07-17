#!/bin/bash

# Quick Setup Script for AWS Deployment
# This script automates AWS infrastructure setup

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
print_success() { echo -e "${GREEN}✅ $1${NC}"; }
print_error() { echo -e "${RED}❌ $1${NC}"; }
print_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }

echo ""
print_info "===== AWS Clinic Management System Setup ====="
echo ""

# Check AWS CLI
if ! command -v aws &> /dev/null; then
    print_error "AWS CLI is not installed"
    print_info "Install from: https://aws.amazon.com/cli/"
    exit 1
fi

print_success "AWS CLI is installed"

# Get AWS configuration
print_info "\nEnter AWS configuration:"
read -p "AWS Region (default: us-east-1): " AWS_REGION
AWS_REGION=${AWS_REGION:-us-east-1}

read -p "AWS Account ID: " AWS_ACCOUNT_ID
read -p "RDS Database Password: " -s RDS_PASSWORD
echo ""

# Create ECR Repository
print_info "\nCreating ECR repository..."
aws ecr create-repository \
    --repository-name clinic-management \
    --region $AWS_REGION \
    2>/dev/null || print_warning "Repository may already exist"
print_success "ECR repository ready"

# Build Docker image
print_info "\nBuilding Docker image..."
docker build -t clinic-management:latest -f docker/Dockerfile . || {
    print_error "Docker build failed"
    exit 1
}
print_success "Docker image built"

# Tag and push to ECR
print_info "\nLogging in to ECR..."
aws ecr get-login-password --region $AWS_REGION | \
    docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com

print_info "Tagging Docker image..."
docker tag clinic-management:latest \
    $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/clinic-management:latest

print_info "Pushing Docker image to ECR..."
docker push $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/clinic-management:latest
print_success "Docker image pushed to ECR"

# Initialize Terraform
print_info "\nInitializing Terraform..."
cd aws/terraform
terraform init || {
    print_error "Terraform init failed"
    exit 1
}

print_info "Planning Terraform deployment..."
terraform plan -var="db_password=$RDS_PASSWORD" -out=tfplan

read -p "Do you want to apply the Terraform plan? (yes/no): " APPLY_TF
if [ "$APPLY_TF" = "yes" ]; then
    print_info "Applying Terraform configuration..."
    terraform apply tfplan
    print_success "Infrastructure created successfully"
    
    # Get outputs
    print_info "\nInfrastructure Details:"
    terraform output
else
    print_warning "Terraform plan saved but not applied"
fi

cd ../..

# Summary
echo ""
print_success "===== Setup Completed ====="
print_info "Next steps:"
print_info "1. Update .env with RDS endpoint from Terraform output"
print_info "2. Run database migration: bash scripts/migrate.sh"
print_info "3. Deploy to ECS: Deploy via GitHub Actions or manually"
print_info ""
print_info "ECR Repository: $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/clinic-management"
print_info "AWS Region: $AWS_REGION"
