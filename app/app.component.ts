import {Component, OnInit} from 'angular2/core';
import {Genstand} from './Genstand';
import {GenstandDetailComponent} from './genstand-detail.component';
import {GenstandService} from './genstand.service';


@Component({
    selector: 'my-app',
  template: `
  <h1>{{title}}</h1>
  <h2>Genstande</h2>
  <ul class="genstande">
  	<li *ngFor="#genstand of genstande"  
     [class.selected]="genstand === valgtGenstand"
     (click)="onSelect(genstand)">
  	<span class="badge">{{genstand.id}}</span> {{genstand.headline}}
	  </li>
  </ul>
  <genstand-detail [genstand]="valgtGenstand">Hej</genstand-detail>
  `,

  styles: [`
  .selected {
    background-color: #CFD8DC !important;
    color: black;
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

  .genstande li.selected {
    color:black;
  }

  .genstande li.selected:hover {
    background-color: #BBD8DC !important;
    color:black;
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
`

  ],
  directives: [GenstandDetailComponent],
  providers: [GenstandService]

})
export class AppComponent implements OnInit {

  public genstande: Genstand[];
  title = 'DDHF Genstandsadministration';
  valgtGenstand: Genstand;

  constructor(private _genstandService: GenstandService) {}

  getGenstande() {
    this._genstandService.getGenstande().subscribe( res => {
    var rj = res.json();

    if (rj.sanity === 'GOOD') {
      this.genstande = rj.data.default;
    }
    });
  }

  ngOnInit() {
    this.getGenstande();
  }

  onSelect(genstand: Genstand) { this.valgtGenstand = genstand; }

}