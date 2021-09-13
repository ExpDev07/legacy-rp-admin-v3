@echo off

rem Don't mind the gazillion lines of code just there to have a pretty and colored output

SETLOCAL

set INFO=[106m[30mINFO [0m [96m
set ERROR=[101m[30mERROR[0m [91m
set DEFAULT=[0m[90m

rem Write the current timestamp (in milliseconds) to tmp/pw_timestamp
set START_TIME=powershell -command "[Math]::Round((Get-Date).ToFileTime()/10000) > $env:TEMP\\pw_timestamp"

rem Subtract the last timestamp from the current timestamp, format it (seconds / milliseconds) and print it in green
set END_TIME=powershell -command "$prev = Get-Content $env:TEMP\\pw_timestamp; $prev_time = $prev -as [double]; $time = [Math]::Round((Get-Date).ToFileTime()/10000) - $prev_time; $sec = [Math]::Floor([decimal]($time / 1000)); if ( $sec -gt 0) { $time = $time - ($sec * 1000); $time = """$($sec)s $($time)"""; }; Write-Host """ [92m$($time)ms[0m[90m""""

%START_TIME%
echo|set /p=%INFO%Reading .env %DEFAULT%.....................
FOR /F "tokens=* eol=#" %%i in ('type deploy.env') do SET %%i
%END_TIME%

%START_TIME%
echo|set /p=%INFO%Testing ssh connection %DEFAULT%...........
ssh -i %SSH_PRIVKEY% -o StrictHostKeyChecking=no %SSH_SERVER% "echo 'Connection successful'" >nul || goto :ssh_error
%END_TIME%

%START_TIME%
echo|set /p=%INFO%Compiling for production %DEFAULT%.........
call npm run production > npm.log 2>&1 || goto :compile_error
del npm.log
%END_TIME%

%START_TIME%
echo|set /p=%INFO%Cleaning up old files %DEFAULT%............
ssh -i %SSH_PRIVKEY% -o StrictHostKeyChecking=no %SSH_SERVER% "rm -rf /var/www/legacy-rp-admin-v3/public/js/*; rm -rf /var/www/legacy-rp-admin-v3/public/css/*; rm /var/www/legacy-rp-admin-v3/public/mix-manifest.json"
%END_TIME%

%START_TIME%
echo|set /p=%INFO%Transferring js files %DEFAULT%............
scp -i %SSH_PRIVKEY% -q -o StrictHostKeyChecking=no -r .\public\js\* %SSH_SERVER%:/var/www/legacy-rp-admin-v3/public/js || goto :transfer_error
%END_TIME%

%START_TIME%
echo|set /p=%INFO%Transferring css files %DEFAULT%...........
scp -i %SSH_PRIVKEY% -q -o StrictHostKeyChecking=no -r .\public\css\* %SSH_SERVER%:/var/www/legacy-rp-admin-v3/public/css || goto :transfer_error
%END_TIME%

%START_TIME%
echo|set /p=%INFO%Transferring mix-manifest.json %DEFAULT%...
scp -i %SSH_PRIVKEY% -q -o StrictHostKeyChecking=no .\public\mix-manifest.json %SSH_SERVER%:/var/www/legacy-rp-admin-v3/public || goto :transfer_error
%END_TIME%

echo|set /p=[30m               [102m UPDATE SUCCESSFUL [0m

goto :end

:ssh_error
echo .
echo %ERROR%Failed to connect via ssh%DEFAULT%
echo %ERROR%Make sure SSH_PRIVKEY and SSH_SERVER are set properly%DEFAULT%
goto :end

:transfer_error
echo .
echo %ERROR%Failed to transfer some files via scp%DEFAULT%
goto :end

:compile_error
echo .
echo %ERROR%Failed to compile (npm run production)%DEFAULT%
echo %ERROR%Check npm.log for more information%DEFAULT%
goto :end

:end
ENDLOCAL
echo|set /p=[0m
pause >nul
