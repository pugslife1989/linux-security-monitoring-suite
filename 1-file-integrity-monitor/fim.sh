#!/usr/bin/env bash

FILES=("/etc/passwd" "/etc/ssh/sshd_config")
BASELINE="/var/lib/fim_baseline.sha256"
LOGFILE="/var/log/integrity.log"

if [ ! -f "$BASELINE" ]; then

for files in "${FILES[@]}"; do

    sha256sum "$files" >> "$BASELINE"

done

else

#RESULT=$(sha256sum -c "$BASELINE" 2>&1)
sha256sum -c "$BASELINE" --status 2>&1

if [ $? -ne 0 ]; then
echo "[ALERT]:CHANGES MADE! : $(date)" | tee -a "$LOGFILE"

fi

fi
