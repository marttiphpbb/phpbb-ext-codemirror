# PhpBB Extension - marttiphpbb CodeMirror (helper ext)

[Topic on phpBB.com](https://www.phpbb.com/community/viewtopic.php?f=456&t=2473266)

## Requirements

* phpBB 3.2.1+
* PHP 7.1+

## Features

This phpBB extension provides a basic integration of the [CodeMirror](http://codemirror.net) code editor for use by other extensions. See [CodeMirror configuration](http://codemirror.net/doc/manual.html#config) for the possible options and commands.
The extension tries to load all required dependencies by inspecting a configuration set in JSON format.

## Screenshot

![Configuration](doc/configuration.png)

## Extra Options and Commands

This extension provides some extra configuration options and commands. All of them are prefixed with "marttiphpbb".

### Border

To provide a border around the CodeMirror editor. Helpful for the light themes against the light background of the ACP.

#### Options

* `marttiphpbbBorderEnabled`: defaults to `false`.
* `marttiphpbbBorderStyle`: defaults to `1px solid lightgrey`

#### Commands

* `marttiphpbbToggleBorder`
* `marttiphpbbEnableBorder`
* `marttiphpbbDisableBorder`

### Full screen

Commands for the CodeMirror "fullScreen" option:

* marttiphpbbToggleFullScreen
* marttiphpbbDisableFullScreen
* marttiphpbbEnableFullScreen

### Submit

This command generates a click event on submit buttons:

* marttiphpbbSubmit

## Limitations

* Configuration is in JSON, so no functions can be defined.
* Not all configuration options are working (yet).
* Only one editor can be loaded in one page (for now).

## Quick Install

You can install this on the latest release of phpBB 3.2 by following the steps below:

* Create `marttiphpbb/codemirror` in the `ext` directory.
* Download and unpack the repository into `ext/marttiphpbb/codemirror`
* Enable `CodeMirror (helper ext)` in the ACP at `Customise -> Manage extensions`.

## Uninstall

* Disable `CodeMirror (helper ext)` in the ACP at `Customise -> Extension Management -> Extensions`.
* To permanently uninstall, click `Delete Data`. Optionally delete the `/ext/marttiphpbb/codemirror` directory.

## Support

* Report bugs and other issues to the [Issue Tracker](https://github.com/marttiphpbb/phpbb-ext-codemirror/issues).

## For extension developers: how to use

### In the ACP controller

(in a normal controller likewise)

```php
class main_module
{
    var $u_action;

    function main($id, $mode)
    {
        global $phpbb_container;

        $ext_manager = $phpbb_container->get('ext.manager');
        $template = $phpbb_container->get('template');

        // ...

        switch($mode)
        {
            case 'your_mode':

                //..

                if ($request->is_set_post('submit'))
                {
                    // ...
                }


                //...

                if ($ext_manager->is_enabled('marttiphpbb/codemirror'))
                {
                    $load = $phpbb_container->get('marttiphpbb.codemirror.load');
                    $load->set_mode('json'); // or javascript, css, html, php, markdown, etc.s
                }

                $template->assign_vars([
                    'CONTENT'  => $content,  // retrieve or set somewhere above.
                    'U_ACTION' => $this->u_action,
                ]);

            break;
        }
    }
}
```

### Template (in ACP or board)

```twig
<textarea name="content" id="content"{{- marttiphpbb_codemirror.data_attr ?? '' -}}>
    {{- CONTENT -}}
</textarea>
```

When this extension is enabled, the `<textarea>` will be hidden and instead a CodeMirror instance is shown. Note that the dashes in `{{- CONTENT -}}` are important. Otherwise unwanted whitespace will be inserted.

## License

[GPL-2.0](license.txt)

([CodeMirror](http://codemirror.net) is licensed under MIT.)
