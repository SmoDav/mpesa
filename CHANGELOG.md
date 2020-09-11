## CHANGELOG

### 2019-09-26 :: v5.0.0

#### NativeCache

 - Takes a custom storage path as first constructor argument. If none is provided it uses the default configuration cache location. >v5 takes the `ConfigurationStore` as the first argument.
 - Uses seconds instead of minutes as ttl to conform to PSR-16 spec.
 - Added a new method `pull` to extract the key and delete it from the cache if present.

#### NativeConfig

 - Takes the path of the custom configuration file location as the first argument. If none is provided it uses the default configuration file location.
 - Moved the extracting of configuration values to the `ConfigurationRepository`.

#### Core

 - The core can now be initialized without passing the Configuration and Cache store argument. Only the Client is required. If either of the Configuration and Cache stores are not provided, the default will be used in vanilla, and the Laravel defaults will be used in Laravel.
 - The account to be used is now set via the `Core` method `useAccount`.

#### Authenticator

 - Removed all static methods.
 - Added a `flushTokens` method to remove all authentication tokens from the store. `$core->auth()->flushTokens();` or `app(Core::class)->auth()->flushTokens();`
 - 

#### Authenticator
 - `Identity`, `Registrar`, `Simulate` and `STK` all receive the `Core` instance as the first constructor argument.