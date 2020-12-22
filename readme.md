# DevertPluginChecker
Compares plugins installed in Shopware with a JSON file. 
If a shop incl. the plugins is versioned via GIT, it can quickly happen that inexperienced people install/update/delete plugins via the backend.
With this plugin, you can enforce the list stored in GIT with a CLI command. For example, deactivate plugins that are not in the list.
In the same way, plugins that are imported via GIT can also be installed/updated automatically.

## Details
- Only install, activate or deactivate Plugins. Never delete or uninstall a plugin.
- Every action (install, activate or deactivate) will be logged (`var/log/pluginchecker_*.log`)
- Show a warning message in the backend plugin manager (Something like "please use git")
- Warning message and text can be de/activated/edited via the plugin settings 

## Usage
`./bin/console pluginchecker:check`
Compare shopware plugins with the checklist.
 
`./bin/console pluginchecker:enforce`
Enforce the checklist. Asks before changes are made. (e.g. de/activate/install plugins).
  
`./bin/console pluginchecker:write`
Write checklist to file.

`./bin/console pluginchecker:update`
Update (local) all plugins.

`./bin/console pluginchecker:delete:prepare`
(NOT FINISHED!) Create dummy plugin if original files already deleted.
It is not possible to uninstall and deactivate a plugin if the files are missing. Because of autoload stuff in shopware this needs to be seperate...

`./bin/console pluginchecker:delete:finish`
(NOT FINISHED!) Delete plugins (Not asking!).


## Deploy Example (e.g. on live)
`deploy.sh`
```
#!/bin/bash

# current dir
dir=$(pwd)

# refresh plugin list
cd $dir && ./bin/console plugin:refresh

# delete plugins
cd $dir && ./bin/console pluginchecker:delete:prepare
cd $dir && ./bin/console pluginchecker:delete:finish

# enforce active-state plugins
cd $dir && ./bin/console plugin:enforce

# update plugins
cd $dir && ./bin/console plugin:update
```

## Todos:
- Delete plugin helper (aus SW)
- Enhance readme with https://github.com/faressoft/terminalizer
- Enhance readme with screenshot of warning
- Show deleted plugins (in Checklist Vorhanden, aber nicht in SW)