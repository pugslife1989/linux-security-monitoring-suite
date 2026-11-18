#!/usr/bin/env bash

FILES=("/etc/passwd" "/etc/ssh/sshd_config")
BASELINE="/var/lib/fim_baseline.sha256"
LOGFILE="/var/log/integrity.log"

#for exercise purpose only, add file hash to BASELINE if it doesnt exist. 
if [ ! -f "$BASELINE" ]; then

for files in "${FILES[@]}"; do

    sha256sum "$files" >> "$BASELINE"

done

else

#compare live file hash with the existing ones
sha256sum -c "$BASELINE" --status 2>&1

if [ $? -ne 0 ]; then
echo "[ALERT]:CHANGES MADE! : $(date)" | tee -a "$LOGFILE"

fi

fi
