{
  description = "Development environment for the project";

  inputs = {
    flake-compat.url = "github:nix-community/flake-compat";
    nixpkgs.url = "github:nixos/nixpkgs/nixos-24.05";
    systems.url = "github:nix-systems/default";
  };

  outputs = { nixpkgs, systems, ... }:
    let

      eachSystem = nixpkgs.lib.genAttrs (import systems);
    in
    {
      devShells = eachSystem (system: {
        default =
          let
            pkgs = import nixpkgs { inherit system; };
          in

          pkgs.mkShell {
            packages = with pkgs; [
              php82
              php82Packages.composer
              # php82Extensions.xml
              # php82Extensions.curl
              # php82Extensions.pcov
              # php82Extensions.mbstring

              nodejs_20
            ];

            shellHook = ''
              echo "=================="
              echo -e "php:"
              echo `${pkgs.php82}/bin/php --version`
              echo "=================="
              echo -e "composer:"
              echo `${pkgs.php82Packages.composer}/bin/composer --version`
              echo "=================="
              echo -e "node:"
              echo `${pkgs.nodejs_20}/bin/node --version`
              echo "=================="
              echo "Installing composer dependencies:"
              echo `${pkgs.php82Packages.composer}/bin/composer install`
              echo 
              echo "Done! 🐘"
              echo "=================="
              echo "Installing node dependencies:"
              echo `${pkgs.nodejs_20}/bin/npm install`
              echo 
              echo "Done! 📦"
              echo
              echo "=================="
              echo " ready to rock! 🚀"
              echo "=================="
              echo
              echo
              echo "NOTES:"
              echo "  - On vscode you can start the services with: 'ctrl + shift + p' and type 'Tasks: Run Task' then 'start services'"
              echo "  - On vscode you can stop the services with:  'ctrl + shift + p' and type 'Tasks: Run Task' then 'stop services'"
              echo "  - From the terminal just run: 'docker compose up -d'  and 'docker compose down' to stop the services"
              echo
              echo
              echo
            '';
          };
      });

      formatter.x86_64-linux = nixpkgs.legacyPackages.x86_64-linux.nixpkgs-fmt;
    };
}
