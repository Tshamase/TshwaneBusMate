# --------------------------------------------------------------
# 1. Base image (Ubuntu 22.04)
# --------------------------------------------------------------
FROM ubuntu:22.04

# Prevent interactive prompts
ENV DEBIAN_FRONTEND=noninteractive

# --------------------------------------------------------------
# 2. Install ALL system packages in ONE layer (caching)
# --------------------------------------------------------------
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        python3 python3-pip python3-venv \
        nodejs npm \
        php php-cli php-fpm php-mysql \
        supervisor \
    && apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# --------------------------------------------------------------
# 3. Create app layout (helps caching)
# --------------------------------------------------------------
WORKDIR /app

# Copy ONLY the files we need for each service (order matters for cache)
COPY django_app/requirements.txt   /app/django_app/
COPY node_app/package*.json        /app/node_app/
COPY supervisord.conf             /etc/supervisor/conf.d/supervisord.conf

# --------------------------------------------------------------
# 4. Install Python deps (cached if requirements.txt unchanged)
# --------------------------------------------------------------
WORKDIR /app/django_app
RUN pip install --no-cache-dir -r requirements.txt

# --------------------------------------------------------------
# 5. Install Node deps (cached if package*.json unchanged)
# --------------------------------------------------------------
WORKDIR /app/node_app
RUN npm install

# --------------------------------------------------------------
# 6. Copy the rest of the code (after deps → cache stays)
# --------------------------------------------------------------
WORKDIR /app
COPY django_app/   /app/django_app/
COPY node_app/     /app/node_app/
COPY php_app/      /app/php_app/        

# --------------------------------------------------------------
# 7. Supervisor config – make sure it points to the right dirs
# --------------------------------------------------------------
# (supervisord.conf is already copied above)

# --------------------------------------------------------------
# 8. Expose ports (Render will forward only ONE – we expose all for local)
# --------------------------------------------------------------
EXPOSE 8000 3000 8080

# --------------------------------------------------------------
# 9. Start everything with Supervisor
# --------------------------------------------------------------
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]