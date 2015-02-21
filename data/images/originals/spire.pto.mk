
# makefile for panorama stitching, created by hugin using the new makefilelib

# Force using cmd.exe
SHELL=C:\Windows\system32\cmd.exe

# Tool configuration
NONA="C:/Program Files/Hugin/bin/nona"
PTSTITCHER="PTStitcher"
PTMENDER="C:/Program Files/Hugin/bin/PTmender"
PTBLENDER="C:/Program Files/Hugin/bin/PTblender"
PTMASKER="C:/Program Files/Hugin/bin/PTmasker"
PTROLLER="C:/Program Files/Hugin/bin/PTroller"
ENBLEND="C:/Program Files/Hugin/bin/enblend"
ENFUSE="C:/Program Files/Hugin/bin/enfuse"
SMARTBLEND="smartblend.exe"
HDRMERGE="C:/Program Files/Hugin/bin/hugin_hdrmerge"
RM=del
EXIFTOOL="C:/Program Files/Hugin/bin/exiftool"

# Project parameters
HUGIN_PROJECTION=2
HUGIN_HFOV=171
HUGIN_WIDTH=1682
HUGIN_HEIGHT=1682

# options for the programs
NONA_LDR_REMAPPED_COMP=-z LZW
NONA_OPTS=
ENBLEND_OPTS= -f898x1049+435+233
ENBLEND_LDR_COMP=--compression=LZW
ENBLEND_EXPOSURE_COMP=--compression=LZW
ENBLEND_HDR_COMP=
HDRMERGE_OPTS=-m avg -c
ENFUSE_OPTS=
EXIFTOOL_COPY_ARGS=-ImageDescription -Make -Model -Artist -WhitePoint -Copyright -GPS:all -DateTimeOriginal -CreateDate -UserComment -ColorSpace -OwnerName -SerialNumber
EXIFTOOL_INFO_ARGS="-Software=Hugin 2013.0.0.0d404a7088e6 built by Matthew Petroff" "-UserComment<$${UserComment}&\#xd;&\#xa;Projection: Equirectangular (2)&\#xd;&\#xa;FOV: 171 x 171&\#xd;&\#xa;Ev: 0.00" -f

# the output panorama
LDR_REMAPPED_PREFIX=spire
LDR_REMAPPED_PREFIX_SHELL="spire"
HDR_STACK_REMAPPED_PREFIX=spire_hdr_
HDR_STACK_REMAPPED_PREFIX_SHELL="spire_hdr_"
LDR_EXPOSURE_REMAPPED_PREFIX=spire_exposure_layers_
LDR_EXPOSURE_REMAPPED_PREFIX_SHELL="spire_exposure_layers_"
PROJECT_FILE=B:/xampp/htdocs/sauerfork-project/data/images/spire.pto
PROJECT_FILE_SHELL="B:/xampp/htdocs/sauerfork-project/data/images/spire.pto"
LDR_BLENDED=spire.tif
LDR_BLENDED_SHELL="spire.tif"
LDR_STACKED_BLENDED=spire_fused.tif
LDR_STACKED_BLENDED_SHELL="spire_fused.tif"
LDR_EXPOSURE_LAYERS_FUSED=spire_blended_fused.tif
LDR_EXPOSURE_LAYERS_FUSED_SHELL="spire_blended_fused.tif"
HDR_BLENDED=spire_hdr.exr
HDR_BLENDED_SHELL="spire_hdr.exr"

# first input image
INPUT_IMAGE_1=B:/xampp/htdocs/sauerfork-project/data/images/screenshot_195211.png
INPUT_IMAGE_1_SHELL="B:/xampp/htdocs/sauerfork-project/data/images/screenshot_195211.png"

# all input images
INPUT_IMAGES=B:/xampp/htdocs/sauerfork-project/data/images/screenshot_195211.png\
B:/xampp/htdocs/sauerfork-project/data/images/screenshot_207112.png
INPUT_IMAGES_SHELL="B:/xampp/htdocs/sauerfork-project/data/images/screenshot_195211.png"\
"B:/xampp/htdocs/sauerfork-project/data/images/screenshot_207112.png"

# remapped images
LDR_LAYERS=spire0000.tif\
spire0001.tif
LDR_LAYERS_SHELL="spire0000.tif"\
"spire0001.tif"

# remapped images (hdr)
HDR_LAYERS=spire_hdr_0000.exr\
spire_hdr_0001.exr
HDR_LAYERS_SHELL="spire_hdr_0000.exr"\
"spire_hdr_0001.exr"

# remapped maxval images
HDR_LAYERS_WEIGHTS=spire_hdr_0000_gray.pgm\
spire_hdr_0001_gray.pgm
HDR_LAYERS_WEIGHTS_SHELL="spire_hdr_0000_gray.pgm"\
"spire_hdr_0001_gray.pgm"

# stacked hdr images
HDR_STACK_0=spire_stack_hdr_0000.exr
HDR_STACK_0_SHELL="spire_stack_hdr_0000.exr"
HDR_STACK_0_INPUT=spire_hdr_0000.exr
HDR_STACK_0_INPUT_SHELL="spire_hdr_0000.exr"
HDR_STACK_1=spire_stack_hdr_0001.exr
HDR_STACK_1_SHELL="spire_stack_hdr_0001.exr"
HDR_STACK_1_INPUT=spire_hdr_0001.exr
HDR_STACK_1_INPUT_SHELL="spire_hdr_0001.exr"
HDR_STACKS_NUMBERS=0 1 
HDR_STACKS=$(HDR_STACK_0) $(HDR_STACK_1) 
HDR_STACKS_SHELL=$(HDR_STACK_0_SHELL) $(HDR_STACK_1_SHELL) 

# number of image sets with similar exposure
LDR_EXPOSURE_LAYER_0=spire_exposure_0000.tif
LDR_EXPOSURE_LAYER_0_SHELL="spire_exposure_0000.tif"
LDR_EXPOSURE_LAYER_0_INPUT=spire_exposure_layers_0000.tif\
spire_exposure_layers_0001.tif
LDR_EXPOSURE_LAYER_0_INPUT_SHELL="spire_exposure_layers_0000.tif"\
"spire_exposure_layers_0001.tif"
LDR_EXPOSURE_LAYER_0_INPUT_PTMENDER=spire0000.tif\
spire0001.tif
LDR_EXPOSURE_LAYER_0_INPUT_PTMENDER_SHELL="spire0000.tif"\
"spire0001.tif"
LDR_EXPOSURE_LAYER_0_EXPOSURE=0
LDR_EXPOSURE_LAYERS_NUMBERS=0 
LDR_EXPOSURE_LAYERS=$(LDR_EXPOSURE_LAYER_0) 
LDR_EXPOSURE_LAYERS_SHELL=$(LDR_EXPOSURE_LAYER_0_SHELL) 
LDR_EXPOSURE_LAYERS_REMAPPED=spire_exposure_layers_0000.tif\
spire_exposure_layers_0001.tif
LDR_EXPOSURE_LAYERS_REMAPPED_SHELL="spire_exposure_layers_0000.tif"\
"spire_exposure_layers_0001.tif"

# stacked ldr images
LDR_STACK_0=spire_stack_ldr_0000.tif
LDR_STACK_0_SHELL="spire_stack_ldr_0000.tif"
LDR_STACK_0_INPUT=spire_exposure_layers_0000.tif
LDR_STACK_0_INPUT_SHELL="spire_exposure_layers_0000.tif"
LDR_STACK_1=spire_stack_ldr_0001.tif
LDR_STACK_1_SHELL="spire_stack_ldr_0001.tif"
LDR_STACK_1_INPUT=spire_exposure_layers_0001.tif
LDR_STACK_1_INPUT_SHELL="spire_exposure_layers_0001.tif"
LDR_STACKS_NUMBERS=0 1 
LDR_STACKS=$(LDR_STACK_0) $(LDR_STACK_1) 
LDR_STACKS_SHELL=$(LDR_STACK_0_SHELL) $(LDR_STACK_1_SHELL) 
DO_LDR_BLENDED=1

all : startStitching $(LDR_BLENDED) 

startStitching : 
	@echo ===========================================================================
	@echo Stitching panorama
	@echo ===========================================================================

clean : 
	@echo ===========================================================================
	@echo Remove temporary files
	@echo ===========================================================================
	-$(RM) $(LDR_LAYERS_SHELL) 

test : 
	@echo ===========================================================================
	@echo Testing programs
	@echo ===========================================================================
	@echo Checking nona...
	@-$(NONA) --help > NUL 2>&1 && echo nona is ok || echo nona failed
	@echo Checking enblend...
	@-$(ENBLEND) -h > NUL 2>&1 && echo enblend is ok || echo enblend failed
	@echo Checking enfuse...
	@-$(ENFUSE) -h > NUL 2>&1 && echo enfuse is ok || echo enfuse failed
	@echo Checking hugin_hdrmerge...
	@-$(HDRMERGE) -h > NUL 2>&1 && echo hugin_hdrmerge is ok || echo hugin_hdrmerge failed
	@echo Checking exiftool...
	@-$(EXIFTOOL) -ver > NUL 2>&1 && echo exiftool is ok || echo exiftool failed

info : 
	@echo ===========================================================================
	@echo ***************  Panorama makefile generated by Hugin       ***************
	@echo ===========================================================================
	@echo System information
	@echo ===========================================================================
	@echo Operating System: Windows 7 (6.1 Service Pack 1)
	@echo Architecture: AMD64
	@echo Number of logical processors: 6
	@echo Physical memory: 8386744 kiB (49%% occupied)
	@echo Free space on disc: 14973 MiB
	@echo Active codepage: 1252 (Western European Windows)
	@echo ===========================================================================
	@echo Output options
	@echo ===========================================================================
	@echo Hugin Version: 2013.0.0.0d404a7088e6 built by Matthew Petroff
	@echo Project file: B:\xampp\htdocs\sauerfork-project\data\images\spire.pto
	@echo Output prefix: spire
	@echo Projection: Equirectangular (2)
	@echo Field of view: 171 x 171
	@echo Canvas dimensions: 1682 x 1682
	@echo Crop area: (435,233) - (1333,1282)
	@echo Output exposure value: 0.00
	@echo Output stacks minimum overlap: 0.700
	@echo Output layers maximum Ev difference: 0.50
	@echo Selected outputs
	@echo Normal panorama
	@echo * Blended panorama
	@echo ===========================================================================
	@echo Input images
	@echo ===========================================================================
	@echo Number of images in project file: 2
	@echo Number of active images: 2
	@echo Image 0: B:\xampp\htdocs\sauerfork-project\data\images\screenshot_195211.png
	@echo Image 0: Size 1920x1080, Exposure: 0.00
	@echo Image 1: B:\xampp\htdocs\sauerfork-project\data\images\screenshot_207112.png
	@echo Image 1: Size 1920x1080, Exposure: 0.00

# Rules for ordinary TIFF_m and hdr output

spire0000.tif : B:/xampp/htdocs/sauerfork-project/data/images/screenshot_195211.png $(PROJECT_FILE) 
	$(NONA) $(NONA_OPTS) $(NONA_LDR_REMAPPED_COMP) -r ldr -m TIFF_m -o $(LDR_REMAPPED_PREFIX_SHELL) -i 0 $(PROJECT_FILE_SHELL)

spire_hdr_0000.exr : B:/xampp/htdocs/sauerfork-project/data/images/screenshot_195211.png $(PROJECT_FILE) 
	$(NONA) $(NONA_OPTS) -r hdr -m EXR_m -o $(HDR_STACK_REMAPPED_PREFIX_SHELL) -i 0 $(PROJECT_FILE_SHELL)

spire0001.tif : B:/xampp/htdocs/sauerfork-project/data/images/screenshot_207112.png $(PROJECT_FILE) 
	$(NONA) $(NONA_OPTS) $(NONA_LDR_REMAPPED_COMP) -r ldr -m TIFF_m -o $(LDR_REMAPPED_PREFIX_SHELL) -i 1 $(PROJECT_FILE_SHELL)

spire_hdr_0001.exr : B:/xampp/htdocs/sauerfork-project/data/images/screenshot_207112.png $(PROJECT_FILE) 
	$(NONA) $(NONA_OPTS) -r hdr -m EXR_m -o $(HDR_STACK_REMAPPED_PREFIX_SHELL) -i 1 $(PROJECT_FILE_SHELL)

# Rules for exposure layer output

spire_exposure_layers_0000.tif : B:/xampp/htdocs/sauerfork-project/data/images/screenshot_195211.png $(PROJECT_FILE) 
	$(NONA) $(NONA_OPTS) $(NONA_LDR_REMAPPED_COMP) -r ldr -e 0 -m TIFF_m -o $(LDR_EXPOSURE_REMAPPED_PREFIX_SHELL) -i 0 $(PROJECT_FILE_SHELL)

spire_exposure_layers_0001.tif : B:/xampp/htdocs/sauerfork-project/data/images/screenshot_207112.png $(PROJECT_FILE) 
	$(NONA) $(NONA_OPTS) $(NONA_LDR_REMAPPED_COMP) -r ldr -e 0 -m TIFF_m -o $(LDR_EXPOSURE_REMAPPED_PREFIX_SHELL) -i 1 $(PROJECT_FILE_SHELL)

# Rules for LDR and HDR stack merging, a rule for each stack

$(LDR_STACK_0) : $(LDR_STACK_0_INPUT) 
	$(ENFUSE) $(ENFUSE_OPTS) -o $(LDR_STACK_0_SHELL) -- $(LDR_STACK_0_INPUT_SHELL)
	-$(EXIFTOOL) -overwrite_original_in_place -TagsFromFile $(INPUT_IMAGE_1_SHELL) $(EXIFTOOL_COPY_ARGS) $(LDR_STACK_0_SHELL)

$(HDR_STACK_0) : $(HDR_STACK_0_INPUT) 
	$(HDRMERGE) $(HDRMERGE_OPTS) -o $(HDR_STACK_0_SHELL) -- $(HDR_STACK_0_INPUT_SHELL)

$(LDR_STACK_1) : $(LDR_STACK_1_INPUT) 
	$(ENFUSE) $(ENFUSE_OPTS) -o $(LDR_STACK_1_SHELL) -- $(LDR_STACK_1_INPUT_SHELL)
	-$(EXIFTOOL) -overwrite_original_in_place -TagsFromFile $(INPUT_IMAGE_1_SHELL) $(EXIFTOOL_COPY_ARGS) $(LDR_STACK_1_SHELL)

$(HDR_STACK_1) : $(HDR_STACK_1_INPUT) 
	$(HDRMERGE) $(HDRMERGE_OPTS) -o $(HDR_STACK_1_SHELL) -- $(HDR_STACK_1_INPUT_SHELL)

$(LDR_BLENDED) : $(LDR_LAYERS) 
	$(ENBLEND) $(ENBLEND_LDR_COMP) $(ENBLEND_OPTS) -o $(LDR_BLENDED_SHELL) -- $(LDR_LAYERS_SHELL)
	-$(EXIFTOOL) -E -overwrite_original_in_place -TagsFromFile $(INPUT_IMAGE_1_SHELL) $(EXIFTOOL_COPY_ARGS) $(EXIFTOOL_INFO_ARGS) $(LDR_BLENDED_SHELL)

$(LDR_EXPOSURE_LAYER_0) : $(LDR_EXPOSURE_LAYER_0_INPUT) 
	$(ENBLEND) $(ENBLEND_EXPOSURE_COMP) $(ENBLEND_OPTS) -o $(LDR_EXPOSURE_LAYER_0_SHELL) -- $(LDR_EXPOSURE_LAYER_0_INPUT_SHELL)
	-$(EXIFTOOL) -overwrite_original_in_place -TagsFromFile $(INPUT_IMAGE_1_SHELL) $(EXIFTOOL_COPY_ARGS) $(LDR_EXPOSURE_LAYER_0_SHELL)

$(LDR_STACKED_BLENDED) : $(LDR_STACKS) 
	$(ENBLEND) $(ENBLEND_LDR_COMP) $(ENBLEND_OPTS) -o $(LDR_STACKED_BLENDED_SHELL) -- $(LDR_STACKS_SHELL)
	-$(EXIFTOOL) -E -overwrite_original_in_place -TagsFromFile $(INPUT_IMAGE_1_SHELL) $(EXIFTOOL_COPY_ARGS) $(EXIFTOOL_INFO_ARGS) $(LDR_STACKED_BLENDED_SHELL)

$(LDR_EXPOSURE_LAYERS_FUSED) : $(LDR_EXPOSURE_LAYERS) 
	$(ENFUSE) $(ENBLEND_LDR_COMP) $(ENFUSE_OPTS) -o $(LDR_EXPOSURE_LAYERS_FUSED_SHELL) -- $(LDR_EXPOSURE_LAYERS_SHELL)
	-$(EXIFTOOL) -E -overwrite_original_in_place -TagsFromFile $(INPUT_IMAGE_1_SHELL) $(EXIFTOOL_COPY_ARGS) $(EXIFTOOL_INFO_ARGS) $(LDR_EXPOSURE_LAYERS_FUSED_SHELL)

$(HDR_BLENDED) : $(HDR_STACKS) 
	$(ENBLEND) $(ENBLEND_HDR_COMP) $(ENBLEND_OPTS) -o $(HDR_BLENDED_SHELL) -- $(HDR_STACKS_SHELL)

$(LDR_REMAPPED_PREFIX)_multilayer.tif : $(LDR_LAYERS) 
	tiffcp $(LDR_LAYERS_SHELL) $(LDR_REMAPPED_PREFIX_SHELL)_multilayer.tif

$(LDR_REMAPPED_PREFIX)_fused_multilayer.tif : $(LDR_STACKS) $(LDR_EXPOSURE_LAYERS) 
	tiffcp $(LDR_STACKS_SHELL) $(LDR_EXPOSURE_LAYERS_SHELL) $(LDR_REMAPPED_PREFIX_SHELL)_fused_multilayer.tif

$(LDR_REMAPPED_PREFIX)_multilayer.psd : $(LDR_LAYERS) 
	PTtiff2psd -o $(LDR_REMAPPED_PREFIX_SHELL)_multilayer.psd $(LDR_LAYERS_SHELL)

$(LDR_REMAPPED_PREFIX)_fused_multilayer.psd : $(LDR_STACKS) $(LDR_EXPOSURE_LAYERS) 
	PTtiff2psd -o $(LDR_REMAPPED_PREFIX_SHELL)_fused_multilayer.psd $(LDR_STACKS_SHELL)$(LDR_EXPOSURE_LAYERS_SHELL)
