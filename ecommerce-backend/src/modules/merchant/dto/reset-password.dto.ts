import { ApiProperty } from '@nestjs/swagger';
import { IsString, IsNotEmpty, MinLength } from 'class-validator';

export class ResetPasswordDto {
  @ApiProperty({ description: '新密码' })
  @IsString()
  @IsNotEmpty()
  @MinLength(6, { message: '密码长度不能少于6位' })
  newPassword: string;
}
