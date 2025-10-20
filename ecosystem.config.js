module.exports = {
  apps: [
    {
      name: "backend-api",
      script: "npm",
      args: "run start:prod",
      cwd: "./ecommerce-backend",
      instances: 1,
      exec_mode: "fork",
      env: {
        NODE_ENV: "development",
        PORT: 3000,
        DB_TYPE: "mysql",
        DB_HOST: "127.0.0.1",
        DB_PORT: 3306,
        DB_USERNAME: "tikshop",
        DB_PASSWORD: "TikShop_MySQL_#2025!9pQwXz",
        DB_DATABASE: "ecommerce"
      },
      error_file: "./logs/backend-error.log",
      out_file: "./logs/backend-out.log",
      log_file: "./logs/backend-combined.log",
      time: true,
      autorestart: true,
      max_restarts: 10,
      min_uptime: "10s",
      watch: false,
      ignore_watch: ["node_modules", "logs"],
      max_memory_restart: "1G",
      restart_delay: 4000,
      kill_timeout: 5000,
      wait_ready: true,
      listen_timeout: 10000
    },
    {
      name: "admin-frontend",
      script: "serve",
      args: "-s dist -l 0.0.0.0:5177",
      cwd: "./admin",
      instances: 1,
      exec_mode: "fork",
      env: {
        NODE_ENV: "production",
        PORT: 5177
      },
      error_file: "./logs/admin-error.log",
      out_file: "./logs/admin-out.log",
      log_file: "./logs/admin-combined.log",
      time: true,
      autorestart: true,
      max_restarts: 10,
      min_uptime: "10s",
      watch: false,
      max_memory_restart: "500M",
      restart_delay: 4000,
      kill_timeout: 5000
    },
    {
      name: "merchant-frontend",
      script: "serve",
      args: "-s dist -l 0.0.0.0:5176",
      cwd: "./merchant",
      instances: 1,
      exec_mode: "fork",
      env: {
        NODE_ENV: "production",
        PORT: 5176
      },
      error_file: "./logs/merchant-error.log",
      out_file: "./logs/merchant-out.log",
      log_file: "./logs/merchant-combined.log",
      time: true,
      autorestart: true,
      max_restarts: 10,
      min_uptime: "10s",
      watch: false,
      max_memory_restart: "500M",
      restart_delay: 4000,
      kill_timeout: 5000
    },
    {
      name: "user-app",
      script: "npm",
      args: "run dev",
      cwd: "./user-app",
      instances: 1,
      exec_mode: "fork",
      env: {
        NODE_ENV: "development",
        PORT: 3001
      },
      error_file: "./logs/user-error.log",
      out_file: "./logs/user-out.log",
      log_file: "./logs/user-combined.log",
      time: true,
      autorestart: true,
      max_restarts: 10,
      min_uptime: "10s"
    }
  ]
};