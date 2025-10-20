import { NestFactory } from '@nestjs/core'
import { AppModule } from './app.module'
import { join } from 'path'
import { NestExpressApplication } from '@nestjs/platform-express'
import { ValidationPipe } from '@nestjs/common'
import { DocumentBuilder, SwaggerModule } from '@nestjs/swagger'
import { TransformInterceptor } from './common/interceptors/transform.interceptor'
import { HttpExceptionFilter } from './common/filters/http-exception.filter'

async function bootstrap() {
  const app = await NestFactory.create<NestExpressApplication>(AppModule, {
    logger: process.env.NODE_ENV === 'production' ? ['error', 'warn'] : ['log', 'error', 'warn', 'debug'],
  })

  // 配置静态文件服务
  app.useStaticAssets(join(__dirname, '..', 'uploads'), {
    prefix: '/uploads/',
  })

  // 全局前缀
  app.setGlobalPrefix(process.env.API_PREFIX || 'api')

  // 启用CORS（支持从环境变量读取）
  const corsOrigin = process.env.CORS_ORIGIN?.split(',').map(s => s.trim()).filter(Boolean) || true
  app.enableCors({
    origin: corsOrigin,
    credentials: true,
    methods: ['GET','POST','PUT','PATCH','DELETE','OPTIONS'],
    allowedHeaders: ['Content-Type','Authorization']
  })

  // 全局验证管道
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true,
      transform: true,
      forbidNonWhitelisted: true,
      transformOptions: { enableImplicitConversion: true },
    }),
  )

  // Swagger文档配置（使用CDN资源）
  console.log('🔧 正在初始化Swagger文档...')
  const config = new DocumentBuilder()
    .setTitle('供货型电商平台 API')
    .setDescription('电商平台后端接口文档')
    .setVersion('1.0')
    .addBearerAuth()
    .build()
  const document = SwaggerModule.createDocument(app, config)
  
  // 使用CDN资源避免静态文件问题
  SwaggerModule.setup('api/docs', app, document, {
    customSiteTitle: 'TikShop API 文档',
    customfavIcon: 'https://unpkg.com/swagger-ui-dist@4.15.5/favicon-32x32.png',
    customCss: '.swagger-ui .topbar { display: none }',
    swaggerOptions: {
      persistAuthorization: true,
    },
    customJs: [
      'https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui-bundle.js',
      'https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui-standalone-preset.js',
    ],
    customCssUrl: [
      'https://unpkg.com/swagger-ui-dist@4.15.5/swagger-ui.css',
    ],
    customJsStr: `
      window.onload = function() {
        const ui = SwaggerUIBundle({
          url: '/api/docs-json',
          dom_id: '#swagger-ui',
          presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
          ],
          layout: "StandaloneLayout",
          deepLinking: true,
          showExtensions: true,
          showCommonExtensions: true
        });
      };
    `,
  })
  console.log('✅ Swagger文档已初始化，访问地址: /api/docs')

  const port = parseInt(process.env.PORT || '3000', 10)
  const host = process.env.HOST || '0.0.0.0'
  await app.listen(port, host)
  console.log(`✅ Backend listening on http://${host}:${port}/${process.env.API_PREFIX || 'api'}`)
}

bootstrap()

