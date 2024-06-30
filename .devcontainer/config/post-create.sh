#! /usr/bin/env nix-shell
#! nix-shell -i bash -p bash gnumake

mkdir -p ${HOME}/.config
mkdir -p ${HOME}/.local/bin
echo "export PATH=\$PATH:${HOME}/.local/bin" >> ${HOME}/.bashrc
# PATH="${PATH}:${HOME}/.local/bin"

# install neovim and lunarvim
bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/rolling/utils/installer/install-neovim-from-release)
LV_BRANCH='release-1.4/neovim-0.9' bash <(curl -s https://raw.githubusercontent.com/LunarVim/LunarVim/release-1.4/neovim-0.9/utils/installer/install.sh) --no-install-dependencies -y

# install zellij
url="https://github.com/zellij-org/zellij/releases/latest/download/zellij-x86_64-unknown-linux-musl.tar.gz"
curl --location "$url" | tar -C "${HOME}/.local/bin" -xz

# echo "# zellij setup" >> ${HOME}/.bashrc
# ${HOME}/.local/bin/zellij setup --generate-auto-start bash >> ${HOME}/.bashrc

# place configurations
ln -s $PWD/.devcontainer/config/nvim $HOME/.config/nvim
ln -s $PWD/.devcontainer/config/lvim $HOME/.config/lvim

exit 0
