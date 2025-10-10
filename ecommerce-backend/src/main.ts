import { NestFactory } from '@nestjs/core';
import { ValidationPipe } from '@nestjs/common';
import { SwaggerModule, DocumentBuilder } from '@nestjs/swagger';
import { AppModule } from './app.module';
import { TransformInterceptor } from './common/interceptors/transform.interceptor';
import { HttpExceptionFilter } from './common/filters/http-exception.filter';
import { join } from 'path';
import { NestExpressApplication } from '@nestjs/platform-express';

async function bootstrap() {
  const app = await NestFactory.create<NestExpressApplication>(AppModule);

  // 配置静态文件服务
  app.useStaticAssets(join(__dirname, '..', 'uploads'), {
    prefix: '/uploads/',
  });

  // 全局前缀
  app.setGlobalPrefix(process.env.API_PREFIX || 'api');

  // 启用CORS
  app.enableCors();

  // 全局验证管道
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true,
      transform: true,
      forbidNonWhitelisted: true,
      transformOptions: {
        enableImplicitConversion: true,
      },
    }),
  );

  // 全局响应拦截器
  app.useGlobalInterceptors(new TransformInterceptor());

  // 全局异常过滤器
  app.useGlobalFilters(new HttpExceptionFilter());

  // Swagger文档配置
  if (process.env.NODE_ENV !== 'production') {
    const config = new DocumentBuilder()
      .setTitle('供货型电商平台 API')
      .setDescription('电商平台后端接口文档')
      .setVersion('1.0')
      .addBearerAuth()
      .addTag('用户模块', '用户注册、登录、个人信息等')
      .addTag('商家模块', '商家入驻、商品管理、订单管理等')
      .addTag('商品模块', '商品展示、分类、搜索等')
      .addTag('订单模块', '订单创建、支付、物流等')
      .addTag('平台管理', '平台商品库、商家审核、财务管理等')
      .build();
    
    const document = SwaggerModule.createDocument(app, config);
    SwaggerModule.setup('api/docs', app, document);
  }

  const port = process.env.PORT || 3000;
  const host = process.env.HOST || '0.0.0.0';
  await app.listen(port, host);

  console.log(`
    ================================================
    🚀 应用启动成功！
    ================================================
    📝 API地址: http://localhost:${port}/api
    📝 外部API地址: http://192.168.0.121:${port}/api
    📚 文档地址: http://localhost:${port}/api/docs
    📚 外部文档地址: http://192.168.0.121:${port}/api/docs
    🌍 环境: ${process.env.NODE_ENV || 'development'}
    🌐 监听地址: ${host}:${port}
    ================================================
  `);
}

bootstrap();

