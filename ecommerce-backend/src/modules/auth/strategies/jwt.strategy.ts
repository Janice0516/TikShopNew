import { ExtractJwt, Strategy } from 'passport-jwt';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';

@Injectable()
export class JwtStrategy extends PassportStrategy(Strategy) {
  constructor(private configService: ConfigService) {
    super({
      jwtFromRequest: ExtractJwt.fromAuthHeaderAsBearerToken(),
      ignoreExpiration: false,
      secretOrKey: configService.get('jwt.secret'),
    });
  }

  async validate(payload: any) {
    if (payload.type === 'admin') {
      const id = payload.adminId;
      return { id, adminId: id, userId: id, merchantId: id, type: 'admin' };
    } else if (payload.type === 'user') {
      const id = payload.userId;
      return { id, userId: id, type: 'user' };
    } else if (payload.type === 'merchant') {
      const id = payload.merchantId;
      return { id, merchantId: id, userId: id, type: 'merchant' };
    }
    return payload;
  }
}

