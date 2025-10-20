import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, Length, IsOptional, IsInt, Min } from 'class-validator';

export class UpdateOrderRemarkDto {
  @ApiProperty({ description: '管理员备注', example: '快递已到达北京分拣中心', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 500, { message: '备注长度不能超过500个字符' })
  adminRemark?: string;

  @ApiProperty({ description: '物流状态', example: '已到达北京分拣中心', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 100, { message: '物流状态长度不能超过100个字符' })
  logisticsStatus?: string;

  @ApiProperty({ description: '快递单号', example: 'SF1234567890', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 50, { message: '快递单号长度不能超过50个字符' })
  trackingNumber?: string;

  @ApiProperty({ description: '物流公司', example: '顺丰速运', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 50, { message: '物流公司长度不能超过50个字符' })
  logisticsCompany?: string;
}

export class OrderLogisticsDto {
  @ApiProperty({ description: '订单ID' })
  @IsNotEmpty({ message: '订单ID不能为空' })
  orderId: string;

  @ApiProperty({ description: '物流状态', example: '已到达北京分拣中心' })
  @IsString()
  @IsNotEmpty({ message: '物流状态不能为空' })
  @Length(1, 100, { message: '物流状态长度为1-100个字符' })
  logisticsStatus: string;

  @ApiProperty({ description: '快递单号', example: 'SF1234567890', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 50, { message: '快递单号长度不能超过50个字符' })
  trackingNumber?: string;

  @ApiProperty({ description: '物流公司', example: '顺丰速运', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 50, { message: '物流公司长度不能超过50个字符' })
  logisticsCompany?: string;

  @ApiProperty({ description: '管理员备注', example: '快递已到达北京分拣中心，预计明天送达', required: false })
  @IsOptional()
  @IsString()
  @Length(0, 500, { message: '备注长度不能超过500个字符' })
  adminRemark?: string;
}

export class QueryOrderDto {
  @ApiProperty({ description: '订单号', required: false })
  @IsOptional()
  @IsString()
  orderNo?: string;

  @ApiProperty({ description: '订单状态', required: false })
  @IsOptional()
  @IsInt()
  orderStatus?: number;

  @ApiProperty({ description: '支付状态', required: false })
  @IsOptional()
  @IsInt()
  payStatus?: number;

  @ApiProperty({ description: '收货人姓名', required: false })
  @IsOptional()
  @IsString()
  receiverName?: string;

  @ApiProperty({ description: '收货人电话', required: false })
  @IsOptional()
  @IsString()
  receiverPhone?: string;

  @ApiProperty({ description: '当前页码', example: 1, default: 1, required: false })
  @IsOptional()
  @IsInt()
  @Min(1)
  page?: number;

  @ApiProperty({ description: '每页数量', example: 10, default: 10, required: false })
  @IsOptional()
  @IsInt()
  @Min(1)
  limit?: number;
}
