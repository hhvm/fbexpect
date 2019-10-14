#!/usr/bin/env bash

if [ "$#" -ne 1 ]; then
    echo "Usage: $0 /path/to/file.hack"
    exit 2
fi

# if(!Str\contains($file_contents, '<<SignedSource>>'))
#   Format the file in place
if ! grep -q "<<SignedSource>>" $1; then
  hackfmt $1 -i
fi
