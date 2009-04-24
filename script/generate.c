#include <stdio.h>
#include <string.h>
int main(int argc, char *argv[]) {
	char buffer[128];
	strcpy(buffer, "./script/generate");
	int i;
	for(i=1;i<argc;i++) {
		strcat(buffer, " ");
		strcat(buffer, argv[i]);
	}
	system(buffer);
}
