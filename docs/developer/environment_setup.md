In an effort to improve poMMo's quality I am trying to automate some development tasks that will hopefully make the codebase better. In order to run these automated tasks there is some setup necessary.

### Node.js

Node.js is a platform that is needed by our task runner; Grunt. It allows you to run applications written in JavaScript in the Desktop. You can get it from [Node's website](http://nodejs.org/).

### Grunt

Grunt is a task runner like make, ant or rake but a litte easier to user and understand. To get it you only need to run **npm install** (after installing node.js) in the root of your poMMo folder.

### Running our tasks

Currently we only have one simple task that lints our php files, you can run it using this command from poMMo's root:

```
grunt
```
