# Base image
FROM ubuntu:22.04

# Prevent timezone prompts
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-venv \
    nodejs npm\
    php php-cli php-fpm php-mysql \
    supervisor \
    && apt-get clean\
    && rm -rf /var/lib/apt/lists/*

# Create working directory
WORKDIR /app

# Copy everything into container
COPY . /app

# -----------------------------
# Install dependencies
# -----------------------------

# Django dependencies
WORKDIR /app/django_app
RUN pip install --no-cache-dir -r requirements.txt

# Node dependencies
WORKDIR /app/node_app
RUN npm install

# -----------------------------
# Configure Supervisor
# -----------------------------
WORKDIR /app
RUN mkdir -p /etc/supervisor/conf.d
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose ports for Render
EXPOSE 8000 3000 8080

# Default command
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
