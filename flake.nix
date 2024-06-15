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
              echo "ready to rock! 🚀"
              echo 
              echo "=================="
              echo `${pkgs.php82Packages.composer}/bin/composer --version`
              echo "=================="
              echo -e "node:"
              echo `${pkgs.nodejs_20}/bin/node --version`
              echo "=================="
            '';
          };
      });

      formatter.x86_64-linux = nixpkgs.legacyPackages.x86_64-linux.nixpkgs-fmt;
    };
}
