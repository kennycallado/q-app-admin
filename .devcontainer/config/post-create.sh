#! /usr/bin/env bash

mkdir -p ${HOME}/.config
mkdir -p ${HOME}/.local/bin
PATH="${PATH}:${HOME}/.local/bin"

# install neovim and lunarvim
# bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/rolling/utils/installer/install-neovim-from-release)
# LV_BRANCH='release-1.4/neovim-0.9' bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/release-1.4/neovim-0.9/utils/installer/install.sh) --no-install-dependencies -y

# place configurations
# ln -s $PWD/.devcontainer/config/nvim $HOME/.config/
# ln -s $PWD/.devcontainer/config/lvim $HOME/.config/

exit 0