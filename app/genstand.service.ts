import {Injectable} from 'angular2/core';
import {Http, HTTP_PROVIDERS, Request, RequestMethod, Headers } from 'angular2/http';


@Injectable()
export class GenstandService {
	
	private apiUrl = "http://fbballin.com/v1/items"
	
	constructor(public http: Http) {}
	getGenstande() {
		return this.http.get(this.apiUrl);
	}
}
