output "database_endpoint" {
  value       = aws_db_instance.clinic_db.endpoint
  description = "The connection endpoint in address:port format"
}

output "database_name" {
  value       = aws_db_instance.clinic_db.db_name
  description = "Name of the database"
}

output "database_username" {
  value       = aws_db_instance.clinic_db.username
  description = "Master username for the database"
}
