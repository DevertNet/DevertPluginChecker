# DevertPluginChecker
Compares plugins installed in Shopware with a JSON file. 
If a shop incl. the plugins is versioned via GIT, it can quickly happen that inexperienced people install/update/delete plugins via the backend.
With this plugin, you can enforce the list stored in GIT with a CLI command. For example, deactivate plugins that are not in the list.
In the same way, plugins that are imported via GIT can also be installed/updated automatically.

## Usage
`./bin/console pluginchecker:check`
Compare shopware plugins with the checklist.
 
`./bin/console pluginchecker:enforce`
Enforce the checklist. Asks before changes are made. (e.g. de/activate/install plugins).
  
`./bin/console pluginchecker:write`
Write checklist to file.


## Todos:
- Readme / https://github.com/faressoft/terminalizer
- Warnung in Plugin Manager im Backend
- Changelog bei jeder ausführung: Was wurde gemacht
- Pugin Icon
- Plugins löschen (aus SW)
- Gelöschte Plugins anzeigen (in Checklist Vorhanden, aber nicht in SW)
- Updates durchführen