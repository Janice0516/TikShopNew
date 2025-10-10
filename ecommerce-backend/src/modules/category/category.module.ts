import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { CategoryController } from './category.controller';
import { SimpleCategoryController } from './simple-category.controller';
import { FixedCategoryController } from './fixed-category.controller';
import { PublicCategoryController } from './public-category.controller';
import { CategoryService } from './category.service';
import { Category } from './entities/category.entity';

@Module({
  imports: [TypeOrmModule.forFeature([Category])],
  controllers: [CategoryController, SimpleCategoryController, FixedCategoryController, PublicCategoryController],
  providers: [CategoryService],
  exports: [CategoryService],
})
export class CategoryModule {}
