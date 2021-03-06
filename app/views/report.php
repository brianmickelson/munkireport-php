<?$obj = new View();$obj->view('partials/head')?>

<? // Extract system_profile info
$machine_info = array('cpu_type' => '?', 'machine_model' => '?', 'physical_memory' => '?', 'current_processor_speed' => '?');
if(isset($report['MachineInfo']['SystemProfile']))
{
	
	foreach($report['MachineInfo']['SystemProfile'] AS $part)
	{
		if(isset($part['_items'][0]) && is_array($part['_items'][0]))
		{
			$machine_info = array_merge($machine_info, $part['_items'][0]);
		}
	}
}
$machine_info = (object) $machine_info;
?>

<h1><?=$client->name?></h1>


<table class="twocol">
  <tbody>
    <td>
      <h2>Machine</h2>

      <table class="client_info">
        <tbody>
			<tr>
				<th>Model:</th>
				<td><?=$machine_info->machine_model?> <?=$machine_info->cpu_type?> <?=$machine_info->current_processor_speed?></td>
			</tr>
			<tr>
				<th>Memory:</th>
				<td><?=$machine_info->physical_memory?></td>
			</tr>
			
          <tr>
            <th>Serial:</th>
            <td><?=$client->serial?></td>
          </tr>
          <tr>
            <th>Remote IP:</th>
            <td><?=$client->remote_ip?></td>
          </tr>
			<?if(isset($report['MachineInfo'])):?>
            <tr>
              <th>Hostname:</th>
              <td><?=isset($report['MachineInfo']['hostname']) ? $report['MachineInfo']['hostname'] : '?'?></td>
            </tr>
            <tr>
              <th>OS:</th>
              <td><?=isset($report['MachineInfo']['os_vers']) ? $report['MachineInfo']['os_vers'] : '?'?> (<?=isset($report['MachineInfo']['arch']) ? $report['MachineInfo']['arch'] : '?'?>)</td>
            </tr>
          <?endif?>
          <tr>
            <th>Free Disk Space:</th>
            <td><?=humanreadablesize($report['AvailableDiskSpace'] * 1024)?></td>
          </tr>
          <tr>
            <th>Console User:</th>
            <td><?=$report['ConsoleUser']?></td>
          </tr>
        </tbody>
      </table>
    </td>
    <td>
      <h2>Munki</h2>

      <table class="client_info">
        <tbody>
          <tr>
            <th>Version:</th>
            <td><?=$report['ManagedInstallVersion']?></td>
          </tr>
          <?if(isset($report['ManifestName'])):?>
            <tr>
              <th>Manifest:</th>
              <td><?=$report['ManifestName']?></td>
            </tr>
          <?endif?>
          <tr>
            <th>Run Type:</th>
            <td><?=$report['RunType']?></td>
          </tr>
          <tr>
            <th>Start:</th>
            <td><?=$report['StartTime']?></td>
          </tr>
          <tr>
            <th>End:</th>
            <td><?=$report['EndTime']?></td>
          </tr>
        </tbody>
      </table>
    </td>
  </tbody>
</table>


<h2 id="errors">Errors &amp; Warnings</h2>

<?if($report['Errors'] OR $report['Warnings']):?>
  
	<?if($report['Errors']):?>
		<pre class="error"><?=implode("\n", $report['Errors'])?></pre>
	<?endif?>
	
	<?if($report['Warnings']):?>
	<pre class="warning"><?=implode("\n", $report['Warnings'])?></pre>
	<?endif?>
	
<?else:?>    
	<p><i>No errors or warnings</i></p>
<?endif?>

<?$package_tables = array(	'Apple Updates' =>'AppleUpdates',
							'Active Installs' => 'ItemsToInstall',
							'Active Removals' => 'ItemsToRemove',
							'Problem Installs' => 'ProblemInstalls')?>

<!--! Package tables -->
<?foreach($package_tables AS $title => $report_key):?>
  <h2><?=$title?></h2>
  
	<?if(isset($report[$report_key]) && $report[$report_key]):?>
	<table class="client_info">
      <thead>
        <tr>
          <th>Name</th>
          <th>Size</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
		<?foreach($report[$report_key] AS $item):?>
        <tr>
          <td>
			<?=isset($item['display_name']) ? $item['display_name'] : $item['name']?>
			<?=isset($item['version_to_install']) ? $item['version_to_install'] : ''?>
			<?=isset($item['installed_version']) ? $item['installed_version'] : ''?>
          </td>
          <td style="text-align: right;"><?=isset($item['installed_size']) ? humanreadablesize($item['installed_size'] * 1024): '?'?></td>
          <td><?=isset($item['install_result']) ? $item['install_result'] : (isset($item['installed']) && $item['installed'] ? 'installed' : "not installed")?></td>
        </tr>
		<?endforeach?>
      </tbody>
    </table>
    <?else:?>
      <p><i>No <?=strtolower($title)?></i></p>
	<?endif?>
<?endforeach?>

<?$package_tables = array(	'Managed Installs' =>'ManagedInstalls')?>

<table class="twocol">
  <tbody>
    <td>
		<?foreach($package_tables AS $title => $report_key):?>
		  <h2><?=$title?></h2>

			<?if(isset($report[$report_key]) && $report[$report_key]):?>
			<table class="client_info">
		      <thead>
		        <tr>
		          <th>Name</th>
		          <th>Size</th>
		          <th>Status</th>
		        </tr>
		      </thead>
		      <tbody>
				<?foreach($report[$report_key] AS $item):?>
		        <tr>
		          <td>
					<?=isset($item['display_name']) ? $item['display_name'] : $item['name']?>
					<?=isset($item['version_to_install']) ? $item['version_to_install'] : ''?>
					<?=isset($item['installed_version']) ? $item['installed_version'] : ''?>
		          </td>
		          <td style="text-align: right;"><?=isset($item['installed_size']) ? humanreadablesize($item['installed_size'] * 1024): '?'?></td>
		          <td><?=$item['installed'] ? 'installed' : "not installed"?></td>
		        </tr>
				<?endforeach?>
		      </tbody>
		    </table>
		    <?else:?>
		      <p><i>No <?=strtolower($title)?></i></p>
			<?endif?>
		<?endforeach?>
    </td>
    <td>
    
<?if(isset($report['managed_uninstalls_list'])):?>
  <h2>Managed Uninstalls</h2>

  <table class="client_info">
    <thead>
      <tr>
        <th>Name</th>
      </tr>
    </thead>
    <tbody>
	<?foreach($report['managed_uninstalls_list'] AS $item):?>
      <tr>
        <td>
          <?=$item?>
        </td>
      </tr>
	<?endforeach?>
    </tbody>
  </table>
<?endif?>

    </td>
  </tbody>
</table>

<!--! test code below -->

<?/*
    import pprint
    if True:
        for k in ("MachineInfo",
                  "ConsoleUser",
                  "AvailableDiskSpace",
                  "ManagedInstallVersion",
                  "ManifestName",
                  "RunType",
                  "StartTime",
                  "EndTime",
                  "Errors",
                  "Warnings",
                  "InstallResults",
                  "ItemsToInstall",
                  "ItemsToRemove",
                  "RemovalResults",
                  "ManagedInstalls",
                  "RemovedItems",
                  "ProblemInstalls",
                  'AppleUpdates',
                  'InstalledItems',
                  'RestartRequired',
                  'managed_installs_list',
                  'managed_uninstalls_list',
                  'managed_updates_list'):
            try:
                del report[k]
            except:
                pass
    pretty_report = pprint.pformat(report)
*/?>

  <h2>Debug</h2>
  <pre py:content="pretty_report">
    <?//print_r($report)?>
  </pre>
<?$obj = new View();$obj->view('partials/foot')?>
