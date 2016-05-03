import {Injectable} from 'angular2/core';
import {Http, HTTP_PROVIDERS, Request, RequestMethod, Headers } from 'angular2/http';
import {Genstand} from './Genstand';
import 'rxjs/Rx';

@Injectable()
export class GenstandService {
	
	private apiUrl: string = "http://fbballin.com/v1/items/";
	private body: string;
	private datingFrom: string[];
	private datingTo: string[];
	private datingReceived: string[];
	
	constructor(public http: Http) {}
	getGenstande() {
		return this.http.get(this.apiUrl);
	}
	
	updateItem(item: Genstand) {
		//this.body = JSON.stringify(item);
		this.body = "headline="+item.headline;
		this.body += "&description="+item.description;
		this.body += "&donator="+item.donator;
		this.body += "&producer="+item.producer;
		this.body += "&zipcode="+item.zipcode;
		
		this.datingFrom = JSON.stringify(item.dating_from).split(",")[0].substring(11, 21).split("-");
		this.body += "&dating_from="+(new Date(Number.parseInt(this.datingFrom[2]), Number.parseInt(this.datingFrom[1])-1,
		Number.parseInt(this.datingFrom[0])).getTime()/1000+7200);
		
		this.datingTo = JSON.stringify(item.dating_to).split(",")[0].substring(11, 21).split("-");
		this.body += "&dating_to="+(new Date(Number.parseInt(this.datingTo[2]), Number.parseInt(this.datingTo[1])-1,
		Number.parseInt(this.datingTo[0])).getTime()/1000+7200);
		
		this.datingReceived = JSON.stringify(item.received_at).split(",")[0].substring(11, 21).split("-");
		this.body += "&received_at="+(new Date(Number.parseInt(this.datingReceived[2]), Number.parseInt(this.datingReceived[1])-1,
		Number.parseInt(this.datingReceived[0])).getTime()/1000+7200);
		
		this.body += "&token=test";
		
		console.log("body: "+this.body);
		
		var headers = new Headers();
  		headers.append('Content-Type', 'application/x-www-form-urlencoded');	
		this.http.post(this.apiUrl + item.id.toString(), this.body, {
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
