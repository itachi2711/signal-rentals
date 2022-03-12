import { Component, OnInit } from '@angular/core';
import { select, Store } from '@ngrx/store';
import { AppState } from '../../reducers';
import { selectorCompanyName } from '../../authentication/authentication.selectors';

@Component({
  selector: 'robi-footer',
  templateUrl: './footer.component.html',
  styleUrls: ['./footer.component.css']
})
export class FooterComponent implements OnInit {
  test: Date = new Date();

  companyName: string;
  constructor(private store: Store<AppState>) {
    this.store.pipe(select(selectorCompanyName)).subscribe(name => this.companyName = name);
  }

  ngOnInit() {
  }

}
