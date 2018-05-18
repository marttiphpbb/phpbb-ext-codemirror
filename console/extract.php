<?php
/**
* phpBB Extension - marttiphpbb codemirror
* @copyright (c) 2018 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\codemirror\console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
	const REQUIRE = [
%require_ary%
	];

	const CSS = [
%css_ary%
	];

	const COMMANDS_FILES = [
%command_ary%
	];

	const OPTIONS_FILES = [
%option_ary%
	];

	const DEFAULT_OPTIONS = [
%default_options%
	];

	const METHODS_FILES = [

	];


}
EOT;

	const EXT_ROOT_PATH = __DIR__ . '/../';

	const REQUIRE_TAG = [
		['require("', '")'],
		['require(\'', '\''],
	];

	const COMMAND_TAG = [
		['CodeMirror.commands.', '= function('],
		['cmds.', '= function('],
	];

	const OPTION_TAG = [
		['.defineOption("', '",'],
		['.defineOption(\'', '\','],
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

		$io->writeln([
			'',
			'<comment>Exctract dependencies.</>',
			'<comment>----------------------</>',
		]);

		$file_dep_ary = $option_dep_ary = $command_dep_ary = [];
		$require_count = 0;

		$dir = self::EXT_ROOT_PATH . 'codemirror';

		$finder = new Finder();
		$files = $finder->files()->in($dir)->exclude(['src', 'component-tools'])->sortByName();

		$files = iterator_to_array($files);

		$io->writeln([
			'File Count: ' . count($files),
			'',
			'<l>File Deps</>',
			'<l>---------</>',
		]);

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
							$option_count++;		
							$option_dep_ary[$option] = $loc;
						}
					}

					if ($command_ary)
					{
						foreach ($command_ary as $command)
						{
							$command_count++;		
							$command_dep_ary[$command] = $loc;
						}
					}
				}

				fclose($handle);

				if (count($loc_require_ary))
				{
					$require_str = '[\'' . implode('\', \'', $loc_require_ary) . '\']';
					$file_dep_ary[$loc]['require'] = $require_str;
					$io->writeln('<info>Loc: </>' . $loc. ' <info>Dep:</> <v>' . $require_str . '</>');
				}
			}
		}

		$io->writeln([
			'Dep count:' . $require_count,
			'',
			'<l>Option deps:</>',
			'<l>------------</>',
		]);

		foreach ($option_dep_ary as $option => $loc)
		{
			$io->writeln('<info>Option: </>' . $option . '<info> Loc: </><v>' . $loc . '</>');
		}

		$io->writeln([
			'Option count: ' . count($option_dep_ary),
			'',
			'<l>Command deps:</>',
			'<l>------------</>',
		]);		

		foreach ($command_dep_ary as $command => $loc)
		{
			$io->writeln('<info>Command: </>' . $command . '<info> Loc: </><v>' . $loc . '</>');
		}

		$io->writeln([
			'Command count: ' . count($command_dep_ary),
			'',
			'<l>Has CSS:</>',
			'<l>--------</>',			
		]);

		$css_count = 0;

		foreach ($file_dep_ary as $loc => $ary)
		{
			if ($ary['js'] && $ary['css'])
			{
				$io->writeln('<v>' . $loc . '</>');
				$css_count++;
			}
		}
		$io->writeln([
			'CSS count: ' . $css_count,
			'',			
		]);		

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

	private function get_require(string $line):array
	{
		return $this->get_tagged_content_ary($line, self::REQUIRE_TAG);
	}

	private function get_option(string $line):array
	{
		return $this->get_tagged_content_ary($line, self::OPTION_TAG);
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
