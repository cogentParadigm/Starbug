#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
int main(int argc, char *argv[]) {
	if (strcmp(argv[1], "dir") == 0) {
		if (!file_exists(argv[2])) execl("/bin/mkdir", "/bin/mkdir", argv[2]);
	} else if (strcmp(argv[1], "sax") == 0) {
		if (!file_exists(argv[2])) execl("/usr/bin/java", "/usr/bin/java", "-jar", "/usr/share/java/saxon/saxon9.jar", argv[2], argv[3], argv[4], (char *) 0);
	} else if (strcmp(argv[1], "copy") == 0) {
		if (!file_exists(argv[2])) execl("/bin/cp", "/bin/cp", argv[3], argv[2]);
	}
}
int file_exists(const char * filename) {
	FILE * file = fopen(filename, "r");
	if (file != NULL) {
		fclose(file);
    return 1;
  }
  return 0;
}
