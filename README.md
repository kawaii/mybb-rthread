# mybb-rthread
Very simple MyBB plugin to select a random thread in a forum from the last X days.

### Dependencies
- PHP >= 7.2
- https://github.com/frostschutz/MyBB-PluginLibrary

### Widgets
 - `{$rthread_button}` accessible within the `forumdisplay` template structure

### Plugin management events
- **Install:**
  - Settings populated
- **Uninstall:**
  - Settings deleted
- **Activate:**
  - Templates inserted
- **Deactivate:**
  - Templates removed

### Development mode
The plugin can operate in development mode, where plugin templates are being fetched directly from the `templates/` directory - set `rthread\DEVELOPMENT_MODE` to `true` in `inc/plugins/rthread.php`.
