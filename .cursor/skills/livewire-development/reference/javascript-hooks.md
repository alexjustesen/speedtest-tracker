# Livewire 4 JavaScript Integration

## Interceptor System (v4)

### Intercept Messages

```js
Livewire.interceptMessage(({ component, message, onFinish, onSuccess, onError }) => {
    onFinish(() => { /* After response, before processing */ });
    onSuccess(({ payload }) => { /* payload.snapshot, payload.effects */ });
    onError(() => { /* Server errors */ });
});
```

### Intercept Requests

```js
Livewire.interceptRequest(({ request, onResponse, onSuccess, onError, onFailure }) => {
    onResponse(({ response }) => { /* When received */ });
    onSuccess(({ response, responseJson }) => { /* Success */ });
    onError(({ response, responseBody, preventDefault }) => { /* 4xx/5xx */ });
    onFailure(({ error }) => { /* Network failures */ });
});
```

### Component-Scoped Interceptors

```blade
<script>
    this.$intercept('save', ({ component, onSuccess }) => {
        onSuccess(() => console.log('Saved!'));
    });
</script>
```

## Magic Properties

- `$errors` - Access validation errors from JavaScript
- `$intercept` - Component-scoped interceptors