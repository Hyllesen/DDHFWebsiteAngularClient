import {Component} from 'angular2/core';

export class Genstand {
	id: number;
	headline: string;
}

@Component({
    selector: 'my-app',
	template: `
  <h1>{{title}}</h1>
  <h2>Genstande</h2>
  <ul class="genstande">
  	<li *ngFor="#genstand of genstande" >
  	<span class="badge">{{genstand.id}}</span> {{genstand.headline}}
	</li>

  </ul>
  <h2>{{genstand.headline}} details!</h2>
  <div><label>id: </label>{{genstand.id}}</div>
  <div>
  	<label>Titel: </label>
  	<input [(ngModel)]="genstand.headline" placeholder="Titel">
  	</div>
  `,

  styles: [`
  .selected {
    background-color: #CFD8DC !important;
    color: white;
  }
  .genstande {
    margin: 0 0 2em 0;
    list-style-type: none;
    padding: 0;
    width: 15em;
  }
  .genstande li {
    cursor: pointer;
    position: relative;
    left: 0;
    background-color: #323941;
    margin: .5em;
    padding: .3em 0;
    height: 1.6em;
    border-radius: 4px;
    color: #B0C9CE;

  }
  .genstande li.selected:hover {
    background-color: #BBD8DC !important;
    color: white;
  }
  .genstande li:hover {
    color: #607D8B;
    background-color: #DDD;
    left: .1em;
  }
  .genstande .text {
    position: relative;
    top: -3px;
  }
  .genstande .badge {
    display: inline-block;
    font-size: small;
    color: white;
    padding: 0.8em 0.7em 0 0.7em;
    background-color: #024454;
    line-height: 1em;
    position: relative;
    left: -1px;
    top: -4px;
    height: 1.8em;
    margin-right: .8em;
    border-radius: 4px 0 0 4px;
  }
`]


})
export class AppComponent {
	public genstande = GENSTANDE;
	title = 'DDHF Genstandsadministration';
	genstand: Genstand = {
		id: 1,
		headline: 'Apple II'
	};
}

var GENSTANDE: Genstand[] = [
	{ "id": 11, "headline": "C64" },
	{ "id": 12, "headline": "Sega dreamcast" },
	{ "id": 13, "headline": "Nintendo" },
	{ "id": 14, "headline": "Amiga A500" },
	{ "id": 15, "headline": "Compaq" },
	{ "id": 16, "headline": "Dell" },
	{ "id": 17, "headline": "HP" },
	{ "id": 18, "headline": "Atari" },
	{ "id": 19, "headline": "Super Nintendo" },
	{ "id": 20, "headline": "Xbox" }
];
