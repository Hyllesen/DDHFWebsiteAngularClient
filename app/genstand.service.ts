import {Injectable} from 'angular2/core';
import {GENSTANDE} from './mock-genstande';

@Injectable()
export class GenstandService {
	getGenstande() {
		return Promise.resolve(GENSTANDE);
	}
}
