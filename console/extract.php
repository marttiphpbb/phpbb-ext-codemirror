<?php
/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Finder\Finder;
use phpbb\console\command\command;
use phpbb\user;
use marttiphpbb\codemirror\util\cnst;

class extract extends command
{
	const FILE = __DIR__ . '/../util/dependencies.php';

	const TEMPLATE_FILE = <<<'EOT'
<?php
/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
* This file was generated with the ext-codemirror:extract command
*/

namespace marttiphpbb\codemirror\util;

class dependencies
{
	// lib/codemirror is omitted from this list as it is required by all.
	const FILES = [
%c_files%];

	const CSS = [
%c_css%];

	const COMMANDS = [
%c_commands%];

	const OPTIONS = [
%c_options%];

	const USE_OPTIONS = [
%c_use_options%];

	const CORE_COMMANDS = [
%c_core_commands%];

	const DEFAULT_KEYMAP = [
%c_default_keymap%];
}
EOT;

	const EXT_ROOT_PATH = __DIR__ . '/../';

	const REQUIRE_TAG = [
		['require("', '")'],
		['require(\'', '\''],
	];

	const COMMAND_TAG = [
		['.commands.', '= function('],
		['cmds.', '= function('],
	];

	const CORE_COMMAND_TAG = [
		['  ', ': '],
	];

	const OPTION_TAG = [
		['.defineOption("', '",'],
		['.defineOption(\'', '\','],
	];

	const USE_OPTION_TAG = [
		['.setOption("', '",'],
		['.setOption(\'', '\''],
		['.getOption("', '")'],
		['.getOption(\'', '\')'],
	];

	public function __construct(user $user)
	{
		parent::__construct($user);
	}

	/**
	* {@inheritdoc}
	*/
	protected function configure()
	{
		$this
			->setName('ext-codemirror:extract')
			->setDescription('For Development: Extract dependency data from CodeMirror repo and own javascript files.')
			->setHelp('This command was created for the development of the marttiphpbb-codemirror extension.')
			->addOption('write', 'w', InputOption::VALUE_NONE, 'Write the util/dependencies.php file')
		;
	}

	/**
	* @param InputInterface
	* @param OutputInterface
	* @return void
	*/
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$io = new SymfonyStyle($input, $output);

		$outputStyle = new OutputFormatterStyle('white', 'black', ['bold']);
		$output->getFormatter()->setStyle('v', $outputStyle);

		$outputStyle = new OutputFormatterStyle('yellow', 'default', ['bold']);
		$output->getFormatter()->setStyle('l', $outputStyle);

		$write = $input->getOption('write');		

		$io->writeln([
			'',
			'<comment>Exctract dependencies.</>',
			'<comment>----------------------</>',
		]);

		$file_dep_ary = $option_dep_ary = $command_dep_ary = [];
		$core_command_ary = $default_keymap_ary = $default_keymap_lines = [];
		$require_count = 0;
		$core_commands_open = $default_keymap_open = false;

		$dir = self::EXT_ROOT_PATH . 'codemirror';

		$finder = new Finder();
		$files = $finder
			->files()
			->in($dir)
			->exclude(['src', 'component-tools'])
			->ignoreVCS(true)
			->ignoreDotFiles(true)
			->sortByName();

		$files = iterator_to_array($files);

		$io->writeln([
			'File Count: ' . count($files),
			'',
			'<l>File Deps</>',
			'<l>---------</>',
		]);

		$c_files = $c_options = $c_commands = $c_css = '';
		$c_use_options = $c_core_commands = '';
		$c_default_keymap = '';

		foreach ($files as $file)
		{
			$rel_path = $file->getRelativePathname();
			list($loc, $ext) = explode('.', $rel_path);
			$file_dep_ary[$loc][$ext] = true;
			$loc_require_ary = [];

			if ($handle = fopen($file, 'r'))
			{
				while (($line = fgets($handle, 4096)) !== false)
				{
					$require_ary = $this->get_require($line);
					$option_ary = $this->get_option($line);
					$command_ary = $this->get_command($line);
					$use_option_ary = $this->get_use_option($line);

					if ($require_ary)
					{
						foreach ($require_ary as $require)
						{
							$cm_require = $this->sanitize_path($require, $loc);
						
							if ($cm_require === 'lib/codemirror')
							{
								continue;
							}
		
							$require_count++;
		
							$loc_require_ary[] = $cm_require;
						}
					}

					if ($option_ary)
					{
						foreach ($option_ary as $option)
						{		
							$option_dep_ary[$option] = $loc;
						}
					}

					if ($use_option_ary)
					{
						foreach ($use_option_ary as $use_option)
						{		
							$use_option_dep_ary[$loc][$use_option] = true;
						}
					}

					if ($command_ary)
					{
						foreach ($command_ary as $command)
						{		
							$command = trim($command);
							$command_dep_ary[$command] = $loc;
						}
					}

					if ($loc !== 'lib/codemirror' || $ext !== 'js')
					{
						continue;
					}

					if (strpos($line, 'var commands = {') === 0)
					{
						$core_commands_open = true;
					}

					if (strpos($line, '};') === 0)
					{
						$core_commands_open = false;
					}

					if ($core_commands_open)
					{
						$command = $this->get_tagged_content_ary($line, self::CORE_COMMAND_TAG);
						$command = $command ? $command[0] : '';

						if (!$command || trim($command) !== $command || !ctype_alpha($command))
						{
							continue;
						}

						$core_command_ary[] = $command;
						$c_core_commands .= $this->get_c_key_value_line($command, 'true');		

						continue;
					}

					if (strpos($line, 'keyMap.') === 0)
					{
						$default_keymap_open = true;
						continue;
					}

					if (strpos($line, '};') === 0)
					{
						$default_keymap_open = false;
					}

					if ($default_keymap_open)
					{
						if (strpos($line, 'fallthrough:') !== false)
						{
							continue;
						}

						$default_keymap_lines[] = trim($line);
						continue;
					}
				}

				fclose($handle);

				if (count($loc_require_ary))
				{
					$require_str = '[\'' . implode('\', \'', $loc_require_ary) . '\']';
					$c_files .= $this->get_c_key_value_line($loc, $require_str);
					$io->writeln('<info>Loc: </>' . $loc. ' <info>Dep:</> <v>' . $require_str . '</>');
				}
			}
		}

		$io->writeln([
			'Dep count:' . $require_count,
			'',
		]);

		/**
		 * 
		 */

		$io->writeln([
			'<l>Option deps:</>',
			'<l>------------</>',
		]);

		foreach ($option_dep_ary as $option => $loc)
		{
			$c_options .= $this->get_c_key_value_line($option, $loc);
			$io->writeln('<info>Option: </>' . $option . '<info> Loc: </><v>' . $loc . '</>');
		}

		$io->writeln([
			'Option count: ' . count($option_dep_ary),
			'',
		]);

		/**
		 * 
		 */

		$io->writeln([
			'<l>Use option deps:</>',
			'<l>----------------</>',
		]);	
		
		$use_option_count = 0;

		foreach ($use_option_dep_ary as $loc => $use_options_keys)
		{
			$use_options = array_keys($use_options_keys);
			$use_option_str = '[\'' . implode('\', \'', $use_options) . '\']';
			$c_use_options .= $this->get_c_key_value_line($loc, $use_option_str);
			$io->writeln('<info>Loc: </>' . $loc . '<info> Options: </><v>' . $use_option_str . '</>');
			$use_option_count += count($use_options);
		}

		$io->writeln([
			'Use option count: ' . $use_option_count,
			'',
		]);

		/**
		 * 
		 */

		$io->writeln([
			'<l>Command deps:</>',
			'<l>------------</>',
		]);

		foreach ($command_dep_ary as $command => $loc)
		{
			$c_commands .= $this->get_c_key_value_line($command, $loc);
			$io->writeln('<info>Command: </>' . $command . '<info> Loc: </><v>' . $loc . '</>');
		}

		$io->writeln([
			'Command count: ' . count($command_dep_ary),
			'',
		]);

		/**
		 * 
		 */

		$io->writeln([
			'<l>Has CSS:</>',
			'<l>--------</>',			
		]);

		$css_count = 0;

		foreach ($file_dep_ary as $loc => $ary)
		{
			if ($ary['js'] && $ary['css'])
			{
				$c_css .= $this->get_c_key_value_line($loc, 'true');
				$io->writeln('<v>' . $loc . '</>');
				$css_count++;
			}
		}
		$io->writeln([
			'CSS count: ' . $css_count,
			'',			
		]);

		/**
		 * 
		 */

		$io->writeln([
			'<l>Core commands:</>',
			'<l>--------------</>',	
		]);

		foreach ($core_command_ary as $command)
		{
			$c_core_commands .= $this->get_c_key_value_line($command, 'true');
			$io->writeln('<v>' . $command . '</>');
		}
		$io->writeln([
			'Core command count: ' . count($core_command_ary),
			'',			
		]);

		/**
		 * 
		 */

		$io->writeln([
			'<l>Default keymap:</>',
			'<l>---------------</>',	
		]);

		foreach ($default_keymap_lines as $line)
		{
			$line = rtrim($line, ',');
			$key_ary = json_decode('{' . $line . '}', true);
			$default_keymap_ary = array_merge($key_ary, $default_keymap_ary);		
		}

		foreach ($default_keymap_ary as $key => $command)
		{
			$c_default_keymap .= $this->get_c_key_value_line($key, $command);
			$io->writeln('<info>Key: </>' . $key . ' <info>Command: </><v>' . $command . '</>');
		}

		$io->writeln([
			'Default keymap count: ' . count($default_keymap_ary),
			'',			
		]);

		/**
		 * 
		 */

		if ($write)
		{
			$search = ['%c_files%', '%c_css%', '%c_options%', 
				'%c_commands%', '%c_use_options%',
				'%c_core_commands%', '%c_default_keymap%',
			];
			$replace = [$c_files . "\t", $c_css . "\t", $c_options . "\t", 
				$c_commands . "\t", $c_use_options . "\t",
				$c_core_commands . "\t", $c_default_keymap . "\t",
			];
			$tpl = str_replace($search, $replace, self::TEMPLATE_FILE);

			file_put_contents(self::FILE, $tpl);

			$io->writeln([
				'<l>File written: marttiphpbb/codemirror/util/dependencies.php</>',
				'',			
			]);
		}
	}

	private function sanitize_path(string $path, string $loc):string 
	{
		$loc = explode('/', $loc);
		array_pop($loc);
		$path = explode('/', $path);
	
		foreach ($path as $k => $p)
		{
			if ($p === '.')
			{
				continue;
			}
			if ($p === '..')
			{
				array_pop($loc);
				continue;
			}
			array_push($loc, $p);
		}

		return implode('/', $loc);
	}

	private function get_c_key_value_line(string $key, string $value):string 
	{
		$value = (strpos($value, '[') === 0 || in_array($value, ['true', 'false'])) ? $value : '\'' . $value . '\'';
		return "\t\t'" . $key . "' => " . $value . ",\n";
	}

	private function get_require(string $line):array
	{
		return $this->get_tagged_content_ary($line, self::REQUIRE_TAG);
	}

	private function get_option(string $line):array
	{
		return $this->get_tagged_content_ary($line, self::OPTION_TAG);
	}

	private function get_use_option(string $line):array
	{
		return $this->get_tagged_content_ary($line, self::USE_OPTION_TAG);
	}

	private function get_command(string $line):array
	{
		return $this->get_tagged_content_ary($line, self::COMMAND_TAG);
	}

	private function get_tagged_content_ary(string $line, array $tags):array
	{
		$deps = [];

		foreach ($tags as $tag)
		{
			$offset = 0;

			while ($ary = $this->get_content_between_tags($line, $tag[0], $tag[1], $offset))
			{
				list($dep, $offset) = $ary;
				$deps[] = $dep;
			}
		}

		return $deps;
	}

	private function get_content_between_tags(string $string, string $start_tag, string $end_tag, int $offset = 0):array
	{
		$start = strpos($string, $start_tag, $offset);

		if ($start === false)
		{
			return [];
		}

		$start += strlen($start_tag);

		$end = strpos($string, $end_tag, $start);

		if ($end === false)
		{
			return [];
		}

		return [substr($string, $start, $end - $start), $end];
	}
}
