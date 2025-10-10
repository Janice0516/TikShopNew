import {
  ExceptionFilter,
  Catch,
  ArgumentsHost,
  HttpException,
  HttpStatus,
} from '@nestjs/common';
import { Request, Response } from 'express';

@Catch(HttpException)
export class HttpExceptionFilter implements ExceptionFilter {
  catch(exception: HttpException, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();
    const request = ctx.getRequest<Request>();
    const status = exception.getStatus();

    const exceptionResponse: any = exception.getResponse();
    const message =
      typeof exceptionResponse === 'string'
        ? exceptionResponse
        : exceptionResponse.message || '服务器错误';

    // 记录错误日志
    console.error('Exception:', {
      timestamp: new Date().toISOString(),
      path: request.url,
      method: request.method,
      status,
      message,
    });

    response.status(status).json({
      code: status,
      message: Array.isArray(message) ? message.join(', ') : message,
      data: null,
      timestamp: Date.now(),
    });
  }
}

@Catch()
export class AllExceptionsFilter implements ExceptionFilter {
  catch(exception: unknown, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();
    const request = ctx.getRequest<Request>();

    const status =
      exception instanceof HttpException
        ? exception.getStatus()
        : HttpStatus.INTERNAL_SERVER_ERROR;

    const message =
      exception instanceof HttpException
        ? exception.message
        : '服务器内部错误';

    console.error('Unhandled Exception:', {
      timestamp: new Date().toISOString(),
      path: request.url,
      method: request.method,
      error: exception,
    });

    response.status(status).json({
      code: status,
      message,
      data: null,
      timestamp: Date.now(),
    });
  }
}

