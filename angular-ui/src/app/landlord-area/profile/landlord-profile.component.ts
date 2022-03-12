import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { LandlordProfileModel } from './model/landlord-profile.model';
import { LandlordProfileService } from './data/landlord-profile-service';
import { NotificationService } from '../../shared/notification.service';
import { Store } from '@ngrx/store';
import { AppState } from '../../reducers';
import { AuthActions } from '../../authentication/action-types';

@Component({
  selector: 'robi-user-profile',
  templateUrl: './landlord-profile.component.html',
  styleUrls: ['./landlord-profile.component.css']
})
export class LandlordProfileComponent implements OnInit {
  form: FormGroup;
  formErrors: any;
  loader = false;

  profile: LandlordProfileModel;
  constructor(private store: Store<AppState>, private fb: FormBuilder, private route: ActivatedRoute,
              private landlordProfileService: LandlordProfileService,
              private notification: NotificationService) {
    this.form = this.fb.group({
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
  prePopulateForm(profile: LandlordProfileModel) {
    this.profile = profile;

    this.form.patchValue({
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
    this.landlordProfileService.update(body)
        .subscribe((data) => {
              this.loader = false;
              // notify success
              this.notification.showNotification('success', 'Success !! Profile has been updated.');
              this.store.dispatch(AuthActions.actionLogout());
            },
            (error) => {
              this.loader = false;
              // An array of all form errors as returned by server
              this.formErrors = error;

              if (this.formErrors) {
                // loop through from fields, If has an error, mark as invalid so mat-error can show
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
