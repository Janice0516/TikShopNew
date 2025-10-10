import { Module } from '@nestjs/common';
import { TestController } from './test.controller';
import { TestCategoryController } from './test-category.controller';

@Module({
  controllers: [TestController, TestCategoryController],
})
export class TestModule {}

