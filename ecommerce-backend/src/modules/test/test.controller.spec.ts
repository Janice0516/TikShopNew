import { Test, TestingModule } from '@nestjs/testing';
import { TestController } from './test.controller';

describe('TestController', () => {
  let controller: TestController;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [TestController],
    }).compile();

    controller = module.get<TestController>(TestController);
  });

  it('should be defined', () => {
    expect(controller).toBeDefined();
  });

  describe('testAdminLogin', () => {
    it('should return success for valid admin credentials', async () => {
      const result = await controller.testAdminLogin({
        username: 'admin',
        password: '123456',
      });

      expect(result.code).toBe(200);
      expect(result.message).toBe('登录成功');
      expect(result.data.token).toBe('test-admin-token-123456');
    });

    it('should return error for invalid admin credentials', async () => {
      const result = await controller.testAdminLogin({
        username: 'wrong',
        password: 'wrong',
      });

      expect(result.code).toBe(401);
      expect(result.message).toBe('用户名或密码错误');
    });
  });

  describe('testUserLogin', () => {
    it('should return success for valid user credentials', async () => {
      const result = await controller.testUserLogin({
        phone: '13800138000',
        password: '123456',
      });

      expect(result.code).toBe(200);
      expect(result.message).toBe('登录成功');
      expect(result.data.token).toBe('test-user-token-123456');
    });

    it('should return error for invalid user credentials', async () => {
      const result = await controller.testUserLogin({
        phone: 'wrong',
        password: 'wrong',
      });

      expect(result.code).toBe(401);
      expect(result.message).toBe('手机号或密码错误');
    });
  });

  describe('testStatus', () => {
    it('should return service status', async () => {
      const result = await controller.testStatus();

      expect(result.code).toBe(200);
      expect(result.message).toBe('服务正常');
      expect(result.data.status).toBe('running');
    });
  });
});
