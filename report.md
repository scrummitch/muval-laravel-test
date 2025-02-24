General
- App should always configure these in AppServiceProvider
```php 
Model::preventAccessingMissingAttributes();
Model::preventSilentlyDiscardingAttributes();

Model::preventLazyLoading();

if ($this->app->isProduction()) {
    Model::handleLazyLoadingViolationUsing(fn ($model, $relation) => someReportingFunction(...));
}
```
- Blade view templates should probably use a re-usable layout
- storage/.DS_Store, bootstrap/.DS_Store, /.DS_Store should probably all be omitted from git 
- 
LoginController@login
- isn't RESTful, doesn't make sense to have front-end user registration in an API app  
  - *move this to an api controller for the SPA*
  - *Update naming to something like* `SessionsController@store` 

RegistrationController@login 
- similar issues to @login
- password doesn't have any complexity rules
  - *add minimum strength requirements and list them on registration page*
- User creation ignores validation response
  - *use output of request->validate() as cleaned data** 
  - *fat controller alert, refactor to invokable action*

routes/web.php
- login route naming is confusing (called home)
- login session creation endpoint is capitals, not valid.
- logout session method name is capitals, not valid.
- All tasks endpoints could be simplified with Route::resource() 

TasksController - general
- no authz, users can view, update, delete tasks that arent theirs 
- some people prefer controllers to have return types, but its not a big deal as often this is handled in middleware

TasksController@index
- Tasks should probably be scoped to user 
- Loop within controller not required, doesn't do anything
- In view
  - View lazy loads user relationship, should use Task::with('user') in controller
  - Generally should ONLY support delete via POST 

TasksController@store 
- Direct access to $_ globals isn't great, better to inject the Http request object 
- No validation 
- Direct DB insert is crazy this person should get fired
- Task model needs user_id and status (not null)

TasksController@edit 
- Should be injecting Task model and doing authz with a Policy
- DB::select returns a query builder? But this shouldnt even be there anyway
- truthy checks should really only be done on truthy objects (true/false)
- returning a string from a controller is strange 
- direct array access is usually a recipe for disaster, use Arr::get() 
- in the view:
    - update request should be PATCH (or PUT if replacing all values)
    - inline css often breaks csp rules

TasksController@update
- Same issues as edit page 
- User would probably be expecting some kind of confirmation

TasksController@destroy
- No validation on who is deleting what 
