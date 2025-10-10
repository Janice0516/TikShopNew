{
  "name": "ecommerce-platform",
  "version": "1.0.0",
  "description": "International E-commerce Platform",
  "scripts": {
    "start": "pm2 start ecosystem.config.js",
    "stop": "pm2 stop ecosystem.config.js",
    "restart": "pm2 restart ecosystem.config.js",
    "reload": "pm2 reload ecosystem.config.js",
    "delete": "pm2 delete ecosystem.config.js",
    "logs": "pm2 logs",
    "status": "pm2 status",
    "monit": "pm2 monit"
  },
  "ecosystem": {
    "apps": [
      {
        "name": "backend-api",
        "script": "npm",
        "args": "run start:prod",
        "cwd": "./ecommerce-backend",
        "instances": 1,
        "exec_mode": "fork",
        "env": {
          "NODE_ENV": "production",
          "PORT": 3000
        },
        "error_file": "./logs/backend-error.log",
        "out_file": "./logs/backend-out.log",
        "log_file": "./logs/backend-combined.log",
        "time": true,
        "autorestart": true,
        "max_restarts": 10,
        "min_uptime": "10s"
      },
      {
        "name": "admin-frontend",
        "script": "npm",
        "args": "run dev",
        "cwd": "./admin",
        "instances": 1,
        "exec_mode": "fork",
        "env": {
          "NODE_ENV": "development",
          "PORT": 5175
        },
        "error_file": "./logs/admin-error.log",
        "out_file": "./logs/admin-out.log",
        "log_file": "./logs/admin-combined.log",
        "time": true,
        "autorestart": true,
        "max_restarts": 10,
        "min_uptime": "10s"
      },
      {
        "name": "merchant-frontend",
        "script": "npm",
        "args": "run dev",
        "cwd": "./merchant",
        "instances": 1,
        "exec_mode": "fork",
        "env": {
          "NODE_ENV": "development",
          "PORT": 5174
        },
        "error_file": "./logs/merchant-error.log",
        "out_file": "./logs/merchant-out.log",
        "log_file": "./logs/merchant-combined.log",
        "time": true,
        "autorestart": true,
        "max_restarts": 10,
        "min_uptime": "10s"
      },
      {
        "name": "user-app",
        "script": "npm",
        "args": "run dev:h5",
        "cwd": "./user-app",
        "instances": 1,
        "exec_mode": "fork",
        "env": {
          "NODE_ENV": "development",
          "PORT": 5176
        },
        "error_file": "./logs/user-error.log",
        "out_file": "./logs/user-out.log",
        "log_file": "./logs/user-combined.log",
        "time": true,
        "autorestart": true,
        "max_restarts": 10,
        "min_uptime": "10s"
      }
    ]
  }
}
