import { IsString, IsNotEmpty, Length } from 'class-validator';

export class AdminLoginDto {
  @IsString()
  @IsNotEmpty()
  @Length(3, 50)
  username: string;

  @IsString()
  @IsNotEmpty()
  @Length(6, 20)
  password: string;
}
