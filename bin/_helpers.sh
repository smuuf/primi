#!/bin/bash -e

C_RESET="\e[0m"
C_GREEN="\e[32m"
C_YELLOW="\e[33m"
C_CYAN="\e[34m"

function info {
	echo -e "${C_CYAN}█${C_RESET} "$1
}

function header {
	echo -e "${C_YELLOW}▄${C_RESET}"
	echo -e "${C_YELLOW}█${C_RESET} $1"
	echo -e "${C_YELLOW}▀${C_RESET}"
}
