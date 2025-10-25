import { IsString, IsOptional, IsUrl } from 'class-validator';
import { ApiProperty } from '@nestjs/swagger';

export class UpdateCategoryImageDto {
  @ApiProperty({ description: '分类图片URL', required: false })
  @IsOptional()
  @IsString()
  @IsUrl({}, { message: '图片URL格式不正确' })
  image_url?: string;

  @ApiProperty({ description: '图标CSS类名', required: false })
  @IsOptional()
  @IsString()
  icon_class?: string;

  @ApiProperty({ description: '分类名称', required: false })
  @IsOptional()
  @IsString()
  name?: string;

  @ApiProperty({ description: '分类描述', required: false })
  @IsOptional()
  @IsString()
  description?: string;
}
