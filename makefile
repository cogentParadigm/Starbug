COMPLETIONS_DIR=$(shell pkg-config --variable=completionsdir bash-completion)

install:
	cp usr/bin/starbug /usr/bin/
	cp usr/bin/sb /usr/bin/
	if [ -d $(COMPLETIONS_DIR) ]; then cp etc/bash_completion.d/sb $(COMPLETIONS_DIR)/; fi
