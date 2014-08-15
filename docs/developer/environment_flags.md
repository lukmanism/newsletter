Following a continuos integration model we try to keep all our features on a single master branch that should be kept stable all of the time.

To develop big features that consist of multiple commits we use environment flags to enable functionality just when **poMMo** is running in a development environment(ei. POMMO_ENV environment variable is set to dev). This allows us to keep all code continuosly integrated in a single branch and not have to worry about merging a big feature in the future.

Preventing a feature from seing light until ready is as easy as an if condition:

```
if ('dev' === getenv('POMMO_ENV')) {
    // Show some unfinished functionality
}
```

Once the feature has been finished and is ready for production you just need to remove the if conditions and it will be available to everyone.
