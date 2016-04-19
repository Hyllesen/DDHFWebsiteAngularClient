import {Component, Input} from 'angular2/core';
import {Genstand} from './Genstand';

@Component({
  selector: 'genstand-detail',
  template: `
  <div *ngIf="genstand">
    <h2>{{genstand.headline}} Detaljer</h2>
    <div><label>id: </label>{{genstand.id}}</div>
    <div>
      <label>Titel: </label>
      <input [(ngModel)]="genstand.headline" placeholder="Titel"/>
    </div>
  </div>
	`
})
export class GenstandDetailComponent {
	@Input()
	genstand: Genstand;
}