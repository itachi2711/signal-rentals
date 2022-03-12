import { Component} from '@angular/core';
import { TranslateService } from '@ngx-translate/core';
import { NavigationCancel, NavigationEnd, NavigationError, NavigationStart, Router } from '@angular/router';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {

  loading = true;

  constructor(translate: TranslateService, private router: Router) {
    translate.addLangs(['en', 'klingon']);
    translate.setDefaultLang('en');
    translate.use('en');

    router.events.subscribe(event => {
      if (event instanceof NavigationStart) {
        this.loading = true;
      }
      if (event instanceof NavigationEnd || event instanceof NavigationCancel || event instanceof NavigationError) {
        this.loading = false;
      }
    });
  }

}
