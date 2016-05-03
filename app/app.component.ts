import {Component, OnInit} from 'angular2/core';
import {Genstand} from './Genstand';
import {GenstandDetailComponent} from './genstand-detail.component';
import {GenstandService} from './genstand.service';


@Component({
    selector: 'my-app',
  template: `
  <h1>{{title}}</h1>
  <div id="genstandDiv">
    <h2>Genstande</h2>
    <ul class="genstande">
      <li *ngFor="#genstand of genstande"  
      [class.selected]="genstand === valgtGenstand"
      (click)="onSelect(genstand)">
      <span class="badge">{{genstand.id}}</span> {{genstand.headline}}
      </li>
    </ul>
  </div>
  <div>
    <genstand-detail [genstand]="valgtGenstand"></genstand-detail>
  </div>
  `,

  styleUrls: ['app/app.component.css'],
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