import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { NotificationService } from '../shared/notification.service';
import { UserProfileModel } from './model/user-profile.model';
import { UserProfileService } from './data/user-profile-service';
import { AuthActions } from '../authentication/action-types';
import { Store } from '@ngrx/store';
import { AppState } from '../reducers';

@Component({
  selector: 'robi-user-profile',
  templateUrl: './user-profile.component.html',
  styleUrls: ['./user-profile.component.css']
})
export class UserProfileComponent implements OnInit {
  form: FormGroup;
  formErrors: any;
  loader = false;

  profile: UserProfileModel;
  constructor(private store: Store<AppState>, private fb: FormBuilder, private route: ActivatedRoute,
              private userProfileService: UserProfileService, private notification: NotificationService) {
    this.form = this.fb.group({
      role: [{value: '', disabled: true}],
      first_name: ['', [Validators.required,
        Validators.minLength(2)]],
      middle_name: [''],
      last_name: [''],
      email: [''],
      phone: [''],
      country: [''],
      city: [''],
      state: [''],
      physical_address: [''],
      postal_address: [''],
      postal_code: [''],
      current_password: [''],
      password: [''],
      password_confirmation: ['']
    });
  }

  ngOnInit() {
    if (this.route.snapshot.data['profile']) {
      this.profile = this.route.snapshot.data['profile'];
      this.prePopulateForm(this.profile);
    }
  }

  /**
   *
   * @param profile
   */
  prePopulateForm(profile: UserProfileModel) {
    this.profile = profile;

    this.form.patchValue({
      role: this.profile?.role?.display_name,
      first_name: this.profile.first_name,
      middle_name: this.profile.middle_name,
      last_name: this.profile.last_name,
      email: this.profile.email,
      phone: this.profile.phone,
      country: this.profile.country,
      city: this.profile.city,
      state: this.profile.state,
      physical_address: this.profile.physical_address,
      postal_address: this.profile.postal_address,
      postal_code: this.profile.postal_code
    });
  }

  /**
   * Update settings
   */
  update() {
    const body = Object.assign({}, this.profile, this.form.value);
    this.loader = true;
    this.userProfileService.update(body)
        .subscribe((data) => {
              this.loader = false;
              this.notification.showNotification('success', 'Success !! User Profile has been updated.');
              this.store.dispatch(AuthActions.actionLogout());
            },
            (error) => {
              this.loader = false;

              if (error.payment === 0) {
                return;
              }
              this.formErrors = error;
              if (this.formErrors) {
                for (const prop in this.formErrors) {
                  if (this.form) {
                    this.form.controls[prop]?.markAsTouched();
                    this.form.controls[prop].setErrors({incorrect: true});
                  }
                }
              }
            });
  }
}
