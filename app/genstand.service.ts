import {Injectable} from 'angular2/core';
import {Http, HTTP_PROVIDERS, Request, RequestMethod, Headers } from 'angular2/http';
import {Genstand} from './Genstand';
import 'rxjs/Rx';

@Injectable()
export class GenstandService {
	
	private apiUrl: string = "http://fbballin.com/v1/items/";
	private body: string;
	
	constructor(public http: Http) {}
	getGenstande() {
		return this.http.get(this.apiUrl);
	}
	
	updateItem(item: Genstand) {
		//console.log("Kører POST på item id:" + item.id);
		this.body = JSON.stringify(item);
		//this.body = "headline="+item.headline;
		console.log("body: "+this.body);
		var headers = new Headers();
  		headers.append('Content-Type', 'application/x-www-form-urlencoded');	
		this.http.post(this.apiUrl + item.id.toString(), this.body+"&token=test", {
			headers: headers
			})
			.map(res => {
				console.log("HTTP Status code: "+res.status);
				return res.json();
			})
			.subscribe(
			data => console.log(data),
			err => console.error(err),
			() => console.log('POST Complete!')
			);
	}
}
