install:
	cp usr/bin/starbug /usr/bin/
	cp usr/bin/sb /usr/bin/
	if [ -d /etc/bash_completion.d ]; then cp etc/bash_completion.d/sb /etc/bash_completion.d/; fi
