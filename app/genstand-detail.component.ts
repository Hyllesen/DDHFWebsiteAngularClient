import {Component, Input} from 'angular2/core';
import {Genstand} from './Genstand';
import {GenstandService} from './genstand.service';

@Component({
  selector: 'genstand-detail',
  template: `
  <div *ngIf="genstand">
    <h2>{{genstand.headline}}</h2>
    <div><label>id: </label>{{genstand.id}}</div>
    <div>
      <label>Titel: </label>
      <input [(ngModel)]="genstand.headline" placeholder="Titel"/>
    </div>
    <div>
    <label>Beskrivelse:</label>
     <input [(ngModel)]="genstand.description" />
     </div>
    <div>
    <label>Donator:</label>
      <input [(ngModel)]="genstand.donator" />
    </div>
    <div><label>Producent:</label> <input [(ngModel)]="genstand.producer" /></div>
    <div><label>Postnr:</label> <input [(ngModel)]="genstand.zipcode" /></div>    
    <div><label>Modtaget:</label> <input [(ngModel)]="genstand.received_at.pretty" /></div>
    <div><label>Dateret fra:</label> <input [(ngModel)]="genstand.dating_from.pretty" /></div>
    <div><label>Dateret til:</label> <input [(ngModel)]="genstand.dating_to.pretty" /></div>  
    <div><label>Opdateret:</label> {{genstand.updated_at.pretty}}</div> 
    <div><label>Oprettet:</label> {{genstand.created_at.pretty}}</div> 
    <div><label>Billeder:</label> 
      <span *ngFor="#image of genstand.images">
        <a href="{{image.full}}" target="_blank"><img src="{{image.thumb}}" alt="{{image.thumb}}"></a>
      </span> 
    </div>
    <div>
      <button (click)="clickUpdateItem($event)">Opdatér genstand i backend</button>
    </div>
  </div>
	`,
  providers: [GenstandService]
})
export class GenstandDetailComponent {
	@Input()
	genstand: Genstand;
  
  constructor(private _genstandService: GenstandService) {}
  
  clickUpdateItem(event) {
    this._genstandService.updateItem(this.genstand);
  }
}