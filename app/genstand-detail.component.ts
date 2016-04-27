import {Component, Input} from 'angular2/core';
import {Genstand} from './Genstand';

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
    <div><label>Postnr:</label> {{genstand.zipcode}}</div>    
    <div><label>Oprettet:</label> {{genstand.created_at}}</div>
    <div><label>Producent:</label> {{genstand.producer}}</div>
    <div><label>Dateret fra:</label> {{genstand.dating_from}}</div>
    <div><label>Dateret til:</label> {{genstand.dating_to}}</div>   
    <div><label>Billeder:</label> 
      <span *ngFor="#image of genstand.images">
        <a href="{{image.full}}"><img src="{{image.thumb}}" alt="{{image.thumb}}"></a>
      </span> 
    </div>      
  </div>
	`
})
export class GenstandDetailComponent {
	@Input()
	genstand: Genstand;
}