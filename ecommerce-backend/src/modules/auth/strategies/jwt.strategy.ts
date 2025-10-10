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
      return { id: payload.adminId, type: payload.type };
    } else if (payload.type === 'user') {
      return { id: payload.userId, type: payload.type };
    } else if (payload.type === 'merchant') {
      return { id: payload.merchantId, type: payload.type };
    }
    return payload;
  }
}

